<?php

namespace App\Http\Controllers\App;

use App\Models\Invoice;
use App\Models\InvoiceCommission;
use App\Models\InvoiceSetting;
use app\Library\AppHelper;
use app\Library\ServiceHelper;
use App\Models\Commission;
use App\Models\Country;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Yajra\DataTables\Facades\DataTables;


class InvoiceController extends Controller
{
    function index(Request $request)
    {
        $page = auth()->user()->group_id == 1 ? "index-admin" : "index-users";
        $users = [];
        $invoices = [];
        $month = '';
        $year = '';
        if ($request->period != "") {
            $exploded = explode(' ', $request->period);
            $rangeFrom = $exploded[0] . " 00:00:00";
            $rangeEnd = $exploded[1] . " 23:59:59";
        } elseif ($request->year != "" || $request->month != "") {
            $month = $request->month;
            $year = $request->year;
        } else {
            $date = Carbon::now();
            $invoiceFor = $date->subMonth()->format('Y-m');
            $start = new \Carbon\Carbon('first day of last month');
            $end = new \Carbon\Carbon('last day of last month');
            $rangeFrom = $start->toDateString() . " 00:00:00";
            $rangeEnd = $end->toDateString() . " 23:59:59";
//            dd($invoiceFor);
            $exploded = explode("-",$invoiceFor);
//            dd($exploded);
            $month = ltrim($exploded[1],0);
            $year = $exploded[0];
        }
        $invoices_qry = Invoice::join('users', 'users.id', 'invoices.user_id');

        if (auth()->user()->group_id == 1) {
            // Get users with group_id 4 and active status
            $users = User::whereIn('group_id', [4])
                ->where('status', 1)
                ->select('id', 'username')
                ->get();

            // Apply the filter for users with group_id 4
            $invoices_qry->whereIn('invoices.user_id', $users->pluck('id'));

            // Apply the grouping for these users only
            $invoices = $invoices_qry
                ->select('invoices.*', 'users.username', \DB::raw('SUM(invoices.total_amount) as total_amount'))
                ->groupBy('users.username')
                ->orderBy('id', 'DESC')
                ->paginate(100);
        } else {
            // For other users, apply user-specific filtering without grouping
            $invoices_qry->where('user_id', auth()->user()->id);

            $invoices = $invoices_qry
                ->select('invoices.*', 'users.username')
                ->orderBy('id', 'DESC')
                ->paginate(100);
        }
        if(auth()->user()->group_id == 1 || auth()->user()->group_id == 4){
			return view('app.invoices.' . $page, [
				'page_title' => "Manage Invoices",
				'users' => $users,
				'invoices' => $invoices,
				'req_period' => $request->period,
				'req_year' => $year,
				'req_month' => $month,
                'singleornot' => 0,
			]);
		}else{
			return redirect('dashboard')->with('message',trans('common.access_violation'))->with('message_type','warning');
		}
    }

    function fetchPayments(Request $request)
    {
        $page = auth()->user()->group_id == 1 ? "index-admin" : "index-users";
        $users = [];
        $invoices = [];
        $month = '';
        $year = '';
        if ($request->period != "") {
            $exploded = explode(' ', $request->period);
            $rangeFrom = $exploded[0] . " 00:00:00";
            $rangeEnd = $exploded[1] . " 23:59:59";
        } elseif ($request->year != "" || $request->month != "") {
            $month = $request->month;
            $year = $request->year;
        } else {
            $date = Carbon::now();
            $invoiceFor = $date->subMonth()->format('Y-m');
            $start = new \Carbon\Carbon('first day of last month');
            $end = new \Carbon\Carbon('last day of last month');
            $rangeFrom = $start->toDateString() . " 00:00:00";
            $rangeEnd = $end->toDateString() . " 23:59:59";
//            dd($invoiceFor);
            $exploded = explode("-",$invoiceFor);
//            dd($exploded);
            $month = ltrim($exploded[1],0);
            $year = $exploded[0];
        }
        $invoices_qry = Invoice::join('users', 'users.id', 'invoices.user_id');
        if (auth()->user()->group_id == 1) {
            $users = User::whereIn('group_id', [4])
                ->where('status', 1)
                ->select('id', 'username')->get();

            $invoices_qry->where('invoices.year', $year)->where('invoices.month',$month)->groupBy('invoices.period_start');
        }
        else {
            $invoices_qry->where('user_id', auth()->user()->id);
        }

        $invoices = $invoices_qry
            ->select('invoices.*', 'users.username')
            ->orderBy('id', "DESC")->paginate(100);
//        dd(\DB::getQueryLog());(
        if(auth()->user()->group_id == 1 || auth()->user()->group_id == 4){
            return view('app.invoices.' . $page, [
                'page_title' => "Manage Invoices",
                'users' => $users,
                'invoices' => $invoices,
                'req_period' => $request->period,
                'req_year' => $year,
                'req_month' => $month,
                'singleornot' => 1,
            ]);
        }else{
            return redirect('dashboard')->with('message',trans('common.access_violation'))->with('message_type','warning');
        }
    }

    function downloadPayment($id)
    {
        // Fetch the invoice/payment by ID
        $query = Payment::join('transactions', 'transactions.id', 'payments.transaction_id')
            ->join('users', 'users.id', 'payments.user_id')
            ->select([
                'payments.date',
                'payments.id',
                'payments.amount',
                'users.username',
                'users.first_name',
                'users.last_name',
                'users.address',
                'users.cust_id',
                'payments.amount',
                'transactions.id as invoice_ref',
                'transactions.balance',
                'payments.description',
                'payments.received_by'
            ])
            ->where('payments.id', $id)
            ->first(); // Correctly assign the result of the query to a variable

        if (!$query) {
            return redirect()->back()->with('message', 'Payment not found')->with('message_type', 'warning');
        }


//        return view('app.invoices.print-payment-invoice', [
//            'invoice' => $query,
//        ]);
//        // Generate the PDF
        $pdf = PDF::loadView('app.invoices.print-payment-invoice', [
            'invoice' => $query,
        ]);
//
//        // Create a meaningful filename for the PDF
        $fileName = $query->username . " " . $query->invoice_ref;
//
//        // Download the PDF file
        return $pdf->download($fileName . '.pdf');
    }

    function viewInvoice($id, $service)
    {
        $invoice = Invoice::join('users', 'users.id', 'invoices.user_id')
            ->where('invoices.id', $id)
            ->where('service', $service)
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.address'
                , 'users.cust_id', 'invoices.*')
            ->first();
        if (!$invoice) {
            Log::warning('unable to find invoice for invoice id ' . $id);
            return back()->with([
                'message' => "Unable to download your invoice!",
                'message_type' => "warning"
            ]);
        }
        return view('app.invoices.view', [
            'id' => $id,
            'service' => $service
        ]);
    }

    function checkInvoice(Request $request ,$id, $service)
    {
        $users = [];
        $invoices = [];

        // Retrieve the invoice based on the provided service ID
        $invoice = Invoice::where('id', $service)->first();

        // If no invoice is found, redirect with an error message
        if (!$invoice) {
            return redirect('dashboard')->with('message', trans('common.invoice_not_found'))->with('message_type', 'danger');
        }
        if($request->year){
            $year = $request->year; // Extract year
            $month = $request->month;
        }else{
            // Extract the year and month from the period_start
            $dateString = $invoice->period_start;
            $exploded = explode('-', substr($dateString, 0, 10)); // Extract YYYY-MM-DD part
            $year = $exploded[0]; // Extract year
            $month = ltrim($exploded[1], '0'); // Extract month without leading zero
        }


        // Start the query for retrieving related invoices
        $invoices_qry = Invoice::join('users', 'users.id', '=', 'invoices.user_id')
            ->where('invoices.user_id', $id);
        // Group ID 1 specific logic (admin user)
        if (auth()->user()->group_id == 1) {
            // Fetch users with group_id = 4 and status = 1
            $users = User::whereIn('group_id', [4])
                ->where('status', 1)
                ->select('id', 'username')
                ->get();

            // Filter invoices by year and month
            $invoices_qry->where('invoices.year', $year)
                ->where('invoices.month', $month);
        } else {
            // For other users, limit the query to their own invoices
            $invoices_qry->where('invoices.user_id', auth()->user()->id);
        }

        // Fetch the invoices with related user information
        $invoices = $invoices_qry
            ->select('invoices.*', 'users.username')
            ->orderBy('invoices.id', 'DESC')
            ->get();

        // Check user permissions for viewing the invoice
        if (auth()->user()->group_id == 1 || auth()->user()->group_id == 4) {
            return view('app.invoices.index-view-admin', [
                'page_title' => "Manage Invoices",
                'users' => $users,
                'invoices' => $invoices,
                'req_period' => $invoice->period,
                'req_year' => $year,
                'req_month' => $month,
                'service' => $service,
                'id' => $id
            ]);
        } else {
            // Redirect to dashboard if the user lacks permission
            return redirect('dashboard')->with('message', trans('common.access_violation'))->with('message_type', 'warning');
        }
    }

    function downloadInvoice($id, $service)
    {
        $invoice = Invoice::join('users', 'users.id', 'invoices.user_id')
            ->where('invoices.id', $id)
            ->where('service', $service)
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.address', 'users.vat_no as tva_no'
                , 'users.cust_id',  'invoices.*')
            ->first();
        if (!$invoice) {
            Log::warning('unable to find invoice for invoice id ' . $id);
            return back()->with([
                'message' => "Unable to download your invoice!",
                'message_type' => "warning"
            ]);
        }
            $servicePrintData = InvoiceCommission::join('services', 'services.id', 'invoice_commissions.service_id')
                ->where('invoice_commissions.invoice_id', $invoice->id)
                ->select('services.name as service_name', 'invoice_commissions.*')
                ->get();
        $layout = ($service == 'tama') ? "print-tama"
            : ($service == 'each_payment' ? "print-single-payment"
                : ($service == 'payment' ? "print-payment" : "print-calling-card"));

            $pdf = PDF::loadView('app.invoices.' . $layout, [
                'invoice' => $invoice,
                'servicePrintData' => $servicePrintData
            ]);
        $fileName = $invoice->username . " " . $invoice->invoice_ref;
        return $pdf->download($fileName . '.pdf');
    }

    function invoiceSettings()
    {
        $invoice_settings = InvoiceSetting::join('countries', 'countries.id', 'invoice_settings.country_id')
            ->select('countries.nice_name', 'invoice_settings.*')
            ->get();
        return view('app.invoices.settings', [
            'page_title' => "Manage Invoice settings",
            'invoice_settings' => $invoice_settings
        ]);
    }

    function createInvoiceSetting()
    {
        $countries = Country::select('id', 'nice_name')->get();
        return view('app.invoices.create', [
            'countries' => $countries
        ]);
    }

    function storeSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => "required|unique:invoice_settings,country_id",
            'commission' => 'required'
        ]);
        if ($validator->fails()) {
            Log::warning("Invoice setting validation failed");
            return back()->with([
                'message' => "Please fill all fields!",
                'message_type' => "warning"
            ]);
        }
        $setting = new InvoiceSetting();
        $setting->country_id = $request->country_id;
        $setting->commission = $request->commission;
        $setting->created_at = date("Y-m-d H:i:s");
        $setting->save();
        Log::info("Invoice setting created");
        return back()->with([
            'message' => "New Invoice commission was created!",
            'message_type' => "success"
        ]);
    }

    function edit($id)
    {
        $countries = Country::select('id', 'nice_name')->get();
        $setting = InvoiceSetting::findOrFail($id);
        return view('app.invoices.edit', [
            'countries' => $countries,
            'setting' => $setting
        ]);
    }

    function updateSetting(Request $request, $id)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'country_id' => "required",
            'commission' => 'required'
        ]);
        if ($validator->fails()) {
            Log::warning("Invoice setting validation failed");
            return back()->with([
                'message' => "Please fill all fields!",
                'message_type' => "warning"
            ]);
        }
        $setting = InvoiceSetting::findOrFail($id);
        $setting->country_id = $request->country_id;
        $setting->commission = $request->commission;
        $setting->updated_at = date("Y-m-d H:i:s");
        $setting->save();
        Log::info("Invoice setting updated");
        return back()->with([
            'message' => "Invoice commission was updated!",
            'message_type' => "success"
        ]);
    }

    function delete($id)
    {
        $setting = InvoiceSetting::findOrFail($id);
        $setting->delete();
        Log::info("Invoice setting deleted");
        return back()->with([
            'message' => "Invoice commission was removed!",
            'message_type' => 'success'
        ]);
    }

    function generateInvoice()
    {
        // Get the authenticated user's group_id
        $userGroupId = auth()->user()->group_id;

        // Initialize the query
        $query = User::where('status', 1)->where('group_id', 4);

        // Execute the query to get the users
        $users = $query->select('id', 'username')->get();

        // Return the view with the users
        return view('app.invoices.generate-invoice', [
            'users' => $users
        ]);
    }

    function confirmGenerate(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_id' => "required",
            'month' => "required",
            'year' => "required"
        ]);
        if ($validator->fails()) {
            Log::warning("Generate invoice validation failed");
            return back()->withErrors($validator)->with([
                'message' => "Validation failed",
                'message_type' => "warning"
            ]);
        }
        $invoiceFor = $request->year." ".$request->month;
        $exploded_month = explode(' ', $invoiceFor);
//        dd($exploded_month);
        $month = date("F", mktime(0, 0, 0, $exploded_month[1], 1));
        $startDateMonth = trim(new Carbon("first day of $month $exploded_month[0]"));
        $endDateMonth = str_replace("00:00:00", "23:59:59", new Carbon("last day of $month $exploded_month[0]"));
        //exception array bags
        $err_invoice_users = [];
        if (collect($request->user_id)->count() > 0) {
            foreach ($request->user_id as $user) {
                //get access and services
                $user_access = Service::join('user_access', 'user_access.service_id', 'services.id')
                    ->where('user_access.user_id', $user)
                    ->where('user_access.status', '=', 1)
					 ->where('services.id', '!=', 7)
                    ->select('services.*')
                    ->get();
                if (!$user_access) {
                    Log::warning("user $user does not have access for any service");
                    continue;
                }

                //user info
                $userInfo = User::find($user);
                //check user already have invoice
//                $checkInvoice = Invoice::where('month', $exploded_month[1])
//                    ->where('year', $exploded_month[0])
//                    ->where('user_id', $user)
//                    ->where('service', 'tama')
//                    ->first();
//
//                if (!$checkInvoice) {
//                    //tama service invoice amount
//                    $total_amount = User::select([
//                        \DB::raw('sum(orders.order_amount) AS sale_price')
//                    ])
//                        ->join('orders', 'orders.user_id', '=', 'users.id')
//                        ->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
//                        ->where('orders.is_parent_order', 1)
//                        ->where('orders.service_id', "!=", 7)
//                        ->where('users.id', "=", $user)->first();
//
//
//                    if ($total_amount->sale_price == null || $total_amount->sale_price == 0) {
//                        continue;
//                    }
//                    //get last invoice number
//                    $last_invoice_no = Invoice::where('month', $exploded_month[1])
//                        ->where('year', $exploded_month[0])
//                        ->select('count')->orderBy('id', "DESC")->first();
//
//                    $last_invoice = isset($last_invoice_no) && $last_invoice_no->count != 0 ? $last_invoice_no->count + 1 : 1;
//
//
//                    $invoice = new Invoice();
//                    $invoice->user_id = $user;
//                    $invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . "000" . $last_invoice;
//                    $invoice->date = date("Y-m-d H:i:s");
//                    $invoice->month = $exploded_month[1];
//                    $invoice->year = $exploded_month[0];
//                    $invoice->period = str_replace("00:00:00", "", $startDateMonth) . " au " . str_replace("23:59:59", "", $endDateMonth);
//                    $invoice->period_start = $startDateMonth;
//                    $invoice->period_end = $endDateMonth;
//                    $invoice->total_amount = $total_amount->sale_price;
//                    $invoice->commission_amount = 0;
//                    $invoice->grand_total = 0;
//                    $invoice->service = 'tama';
//                    $invoice->count = $last_invoice;
//                    $invoice->created_at = date("Y-m-d H:i:s");
//                    $invoice->save();
//                    $invoice_commission_total_amount = 0;
//                    //calculate service commission
//                    foreach ($user_access as $access) {
//                        $service_total_amount = User::select([
//                            \DB::raw('sum(orders.order_amount) AS sale_price')
//                        ])
//                            ->join('orders', 'orders.user_id', '=', 'users.id')
//                            ->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
//                            ->where('orders.is_parent_order', 1)
//                            ->where('orders.service_id', "=", $access->id)
//                            ->where('users.id', "=", $user)->first();
//
//
//                        if ($service_total_amount->sale_price != null) {
//                            $getUserCommission = ServiceHelper::get_service_commission($user, $access->id);
//                            if($userInfo->group_id == 3){
//                                $getUserCommission = 1;
//                            }
//                            Log::info("user commission $access->id $getUserCommission");
//                            $serviceCommission = ServiceHelper::calculate_commission($service_total_amount->sale_price, $getUserCommission);
//                            $subServiceCommission = $service_total_amount->sale_price - $serviceCommission;
//                            Log::info("user service commission $serviceCommission $subServiceCommission");
//							$r = [
//									'user_id' => $user,
//									'invoice_id' => $invoice->id,
//									'service_id' => $access->id,
//									'total_amount' => round($service_total_amount->sale_price, 2),
//									'commission' => $getUserCommission,
//									'commission_amount' => round($subServiceCommission, 2),
//									'created_at' => now()
//								];
//
//                            $invoice_commission = new InvoiceCommission();
//                            $invoice_commission->user_id = $user;
//                            $invoice_commission->invoice_id = $invoice->id;
//                            $invoice_commission->service_id = $access->id;
//                            $invoice_commission->total_amount = number_format($service_total_amount->sale_price, 2);
//                            $invoice_commission->commission = $getUserCommission;
//                            $invoice_commission->commission_amount = number_format($subServiceCommission, 2);
//                            $invoice_commission->created_at = date("Y-m-d H:i:s");
//                            $invoice_commission->save();
//
//                            $invoice_commission_total_amount += $subServiceCommission;
//                        }
//                    }
//                    //update the invoice
//                    Invoice::where('id', $invoice->id)->update([
//                        'commission_amount' => number_format($invoice_commission_total_amount, 2),
//                        'grand_total' => number_format($total_amount->sale_price - $invoice_commission_total_amount, 2)
//                    ]);
//
//                    Log::info("invoice created for $userInfo->username");
//                } else {
//                    Log::info("Invoices already exists for the user $user");
//                }
               
//				  //check user already have invoice
//                        $checkRetailerInvoice = Invoice::where('month', $exploded_month[1])
//                            ->where('year', $exploded_month[0])
//                            ->where('user_id', $user)
//                            ->where('service', 'calling-card')
//                            ->first();
//                        if (!$checkRetailerInvoice) {
//								$callingCardOrders = User::select([
//									\DB::raw('sum(orders.order_amount) AS sale_price')
//								])
//									->join('orders', 'orders.user_id', '=', 'users.id')
//									->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
//								  ->where('orders.is_parent_order', 1)
//									->where('orders.service_id', "=", 7)
//									->where('users.id', "=", $user)->first();
//								if ($callingCardOrders && $callingCardOrders->sale_price != null) {
//									$last_invoice_no = Invoice::where('month', $exploded_month[1])
//										->where('year', $exploded_month[0])
//										->select('count')->orderBy('id', "DESC")->first();
//									$last_invoice = $last_invoice_no->count + 1;
//									$invoice = new Invoice();
//									$invoice->user_id = $user;
//									$invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . "000" . $last_invoice;
//									$invoice->date = date("Y-m-d H:i:s");
//									$invoice->month = $exploded_month[1];
//									$invoice->year = $exploded_month[0];
//									$invoice->period = str_replace("00:00:00", "", $startDateMonth) . " au " . str_replace("23:59:59", "", $endDateMonth);
//									$invoice->period_start = $startDateMonth;
//									$invoice->period_end = $endDateMonth;
//									$invoice->total_amount = $callingCardOrders->sale_price;
//									$invoice->commission_amount = 0;
//								   $invoice->grand_total = $callingCardOrders->sale_price;
//									$invoice->service = 'calling-card';
//									$invoice->count = $last_invoice;
//									$invoice->created_at = date("Y-m-d H:i:s");
//									$invoice->save();
//								}
//							}

//                        // Check if the payment invoice already exists for the user for this period
//                        $checkPaymentInvoice = Invoice::where('month', $exploded_month[1])
//                            ->where('year', $exploded_month[0])
//                            ->where('user_id', $user)
//                            ->where('service', 'payment')
//                            ->first();
//                        if (!$checkPaymentInvoice) {
//
//                            // Fetch total amount of payments for this user in the specified date range
//                            $totalPayments = \DB::table('payments')
//                                ->where('user_id', $user)
//                                ->whereBetween('date', [$startDateMonth, $endDateMonth])
//                                ->sum('amount');
//
//                            if ($totalPayments > 0) {
//                                // Get the last invoice number
//                                $last_invoice_no = Invoice::where('month', $exploded_month[1])
//                                    ->where('year', $exploded_month[0])
//                                    ->select('count')->orderBy('id', "DESC")->first();
//
//                                $last_invoice = isset($last_invoice_no) && $last_invoice_no->count != 0 ? $last_invoice_no->count + 1 : 1;
//
//                                // Create new payment invoice
//                                $invoice = new Invoice();
//                                $invoice->user_id = $user;
//                                $invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . "000" . $last_invoice;
//                                $invoice->date = date("Y-m-d H:i:s");
//                                $invoice->month = $exploded_month[1];
//                                $invoice->year = $exploded_month[0];
//                                $invoice->period = str_replace("00:00:00", "", $startDateMonth) . " au " . str_replace("23:59:59", "", $endDateMonth);
//                                $invoice->period_start = $startDateMonth;
//                                $invoice->period_end = $endDateMonth;
//                                $invoice->total_amount = $totalPayments;
//                                $invoice->commission_amount = 0;  // Assuming no commission on payments
//                                $invoice->grand_total = $totalPayments;
//                                $invoice->service = 'payment'; // Label this invoice as a payment invoice
//                                $invoice->count = $last_invoice;
//                                $invoice->created_at = date("Y-m-d H:i:s");
//                                $invoice->save();
//
//                                Log::info("Payment invoice created for user $user");
//                            }
//                        } else {
//                            Log::info("Payment invoice already exists for user $user");
//                        }
                        // Check if the payment invoice already exists for the user for this period
                        $checkPaymentInvoice = Invoice::where('month', $exploded_month[1])
                            ->where('year', $exploded_month[0])
                            ->where('user_id', $user)
                            ->where('service', 'each_payment')
                            ->first();
                        if (!$checkPaymentInvoice) {

                            // Fetch individual payments for this user in the specified date range
                            $payments = \DB::table('payments')
                                ->where('user_id', $user)
                                //->where('description', 'not like', '%Refunded Amount of this%')
                                ->whereBetween('date', [$startDateMonth, $endDateMonth])
                                ->get();


                            foreach ($payments as $payment) {
                                // Check if an invoice for this specific payment already exists
                                $existingInvoice = Invoice::where('payment_id', $payment->id)->first();
								if (strpos($payment->description,'Refunded')  === false) {
									if (!$existingInvoice) {
                                    // Get the last invoice number
                                    $last_invoice_no = Invoice::where('month', $exploded_month[1])
                                        ->where('year', $exploded_month[0])
                                        ->select('count')->orderBy('id', "DESC")->first();

                                    $last_invoice = isset($last_invoice_no) && $last_invoice_no->count != 0 ? $last_invoice_no->count + 1 : 1;

                                    // Create new invoice for each payment
                                    $invoice = new Invoice();
                                    $invoice->user_id = $user;
                                    $invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . "000" . $last_invoice;
                                    $invoice->date = $payment->date;
                                    $invoice->month = $exploded_month[1];
                                    $invoice->year = $exploded_month[0];
                                    $invoice->period = str_replace("00:00:00", "", $startDateMonth) . " au " . str_replace("23:59:59", "", $endDateMonth);
                                    $invoice->period_start = $startDateMonth;
                                    $invoice->period_end = $endDateMonth;
                                    $invoice->total_amount = $payment->amount; // Invoice for each individual payment
                                    $invoice->commission_amount = 0;  // Assuming no commission on payments
                                    $invoice->grand_total = $payment->amount;
                                    $invoice->service = 'each_payment'; // Label this invoice as a payment invoice
                                    $invoice->count = $last_invoice;
                                    $invoice->payment_id = $payment->id; // Link the invoice to the payment
                                    $invoice->created_at = date("Y-m-d H:i:s");
                                    $invoice->save();

                                    Log::info("Payment invoice created for user $user for payment id: $payment->id");
                                } else {
                                    Log::info("Payment invoice already exists for user $user for payment id: $payment->id");
                                }
								}else{
									
								}
                            }
                        } else {
                            Log::info("Payment invoice already exists for user $user for the period.");
                        }
            }
        }
        return back()->with([
            'message' => "Invoices generated!",
            'message_type' => "success"
        ]);
    }

    function renderInvoice($id, $service)
    {
        $invoice = Invoice::join('users', 'users.id', 'invoices.user_id')
            ->where('invoices.id', $id)
            ->where('service', $service)
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.address'
                , 'users.cust_id', 'invoices.*')
            ->first();
        if (!$invoice) {
            Log::warning('unable to find invoice for invoice id ' . $id);
            return back()->with([
                'message' => "Unable to download your invoice!",
                'message_type' => "warning"
            ]);
        }
        $fileName = $invoice->username . " " . $invoice->invoice_ref;
        if($service == 'payment'){
            $servicePrintData = Payment::where('payments.user_id', $invoice->user_id)
                ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
                ->whereBetween('payments.date', [$invoice->period_start, $invoice->period_end])
                ->select('payments.*', 'transactions.*')
                ->get();
            $pdf = PDF::loadView('app.invoices.print-payment', [
                'invoice' => $invoice,
                'servicePrintData' => $servicePrintData
            ])->inline($fileName);
        }else{
            $servicePrintData = InvoiceCommission::join('services', 'services.id', 'invoice_commissions.service_id')
                ->where('invoice_commissions.invoice_id', $invoice->id)
                ->select('services.name as service_name', 'invoice_commissions.*')
                ->get();
            $layout = $service == 'tama' ? "print-tama" : "print-calling-card";
            $pdf = PDF::loadView('app.invoices.' . $layout, [
                'invoice' => $invoice,
                'servicePrintData' => $servicePrintData
            ])->inline($fileName);
        }
    }

    function emailInvoice($id, $service, Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'message' => "Unable to send!",
                'message_type' => 'warning'
            ], 400);
        }
        $invoice = Invoice::join('users', 'users.id', 'invoices.user_id')
            ->where('invoices.id', $id)
            ->where('service', $service)
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.address'
                , 'users.cust_id', 'users.tva_no', 'invoices.*')
            ->first();
        if (!$invoice) {
            return response()->json([
                'message' => "Unable to send email!",
                'message_type' => 'warning'
            ], 400);
        }
        if($service == 'payment'){
            $servicePrintData = Payment::where('payments.user_id', $invoice->user_id)
                ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
                ->whereBetween('payments.date', [$invoice->period_start, $invoice->period_end])
                ->select('payments.*', 'transactions.*')
                ->get();
            $pdf = PDF::loadView('app.invoices.print-payment', [
                'invoice' => $invoice,
                'servicePrintData' => $servicePrintData
            ]);
        }else{
            $layout = $service == 'tama' ? "print-tama-services" : "print-calling-card";
            $servicePrintData = InvoiceCommission::join('services', 'services.id', 'invoice_commissions.service_id')
                ->where('invoice_commissions.invoice_id', $invoice->id)
                ->select('services.name as service_name', 'invoice_commissions.*')
                ->get();
            $pdf = PDF::loadView('app.invoices.' . $layout, [
                'invoice' => $invoice,
                'servicePrintData' => $servicePrintData
            ]);
        }
        $fileName = $invoice->username . " " . $invoice->invoice_ref . ".pdf";
        $user = User::find($invoice->user_id);
        try {
            $invoice_email_data = [
                'user' => $user,
                'usage_period' => $invoice->period
            ];
            \Mail::send('emails.invoice', $invoice_email_data, function ($message) use ($user, $invoice_email_data, $invoice, $pdf, $fileName) {
                $message->to($user->email)->from('noreply@tamaexpress.com', 'TamaExpress Reseller System')->subject(config('app.name') . " Tax Invoice " . $invoice->period);
                $message->attachData($pdf->output(), $fileName);
            });
            return response()->json([
                'message' => "Invoice has been sent to " . $user->email . "!",
                'message_type' => 'success'
            ], 200);
        } catch (\Exception $exception) {
            Log::warning("Exception while sending email invoice => " . $exception->getMessage());
            return response()->json([
                'message' => "Unable to send email, exception occured!",
                'message_type' => 'warning'
            ], 400);
        }
    }

    function removeInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        $invoice_commissions = InvoiceCommission::where('invoice_id',$id)->delete();
        Log::info("Invoice $id was removed");
        return back()->with([
            'message' => "Invoice removed!",
            'message_type' => 'success'
        ]);
    }
}

