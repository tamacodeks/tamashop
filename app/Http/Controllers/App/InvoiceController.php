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
use App\Models\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;



class InvoiceController extends Controller
{
    function index(Request $request)
    {
        $page = auth()->user()->group_id == 1 ? "admin-index" : "users-index";
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
//        $exploded_month = explode('-', $invoiceFor);
//        $month = date("F", mktime(0, 0, 0, $exploded_month[1], 1));
//        $startDateMonth = new Carbon("first day of $month $exploded_month[0]");
//        $endDateMonth = new Carbon("last day of $month $exploded_month[0]");
//        \DB::enableQueryLog();
        $invoices_qry = Invoice::join('users', 'users.id', 'invoices.user_id');
        if (auth()->user()->group_id == 1) {
            $users = User::whereIn('group_id', [3, 4])
                ->where('status', 1)
                ->where(function ($query) {
                    $query->where('users.parent_id', '=', '0')
                        ->orWhereNull('users.parent_id');
                })
                ->select('id', 'username')->get();
            $invoices_qry->where(function ($query) {
                $query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            })->where('invoices.year', $year)->where('invoices.month',$month);
        } elseif (auth()->user()->group_id == 3) {
            $invoices_qry->whereBetween('invoices.period_end', [$rangeFrom, $rangeEnd])
                ->where('invoices.user_id', auth()->user()->id);
            $users = User::whereIn('group_id', [4])
                ->where('status', 1)
                ->where('users.parent_id', '=', auth()->user()->id)
                ->select('id', 'username')->get();
        } else {
            $invoices_qry->where('user_id', auth()->user()->id);
        }
        $invoices = $invoices_qry
            ->select('invoices.*', 'users.username')
            ->orderBy('id', "DESC")->paginate(100);
//        dd(\DB::getQueryLog());
//        dd($invoices);
        return view('app.invoices.' . $page, [
            'page_title' => "Manage Invoices",
            'users' => $users,
            'invoices' => $invoices,
            'req_period' => $request->period,
            'req_year' => $year,
            'req_month' => $month
        ]);
    }

    function downloadInvoice($id, $service)
    {
        $invoice = Invoice::join('users', 'users.id', 'invoices.user_id')
            ->where('invoices.id', $id)
            ->where('service', $service)
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.address'
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
        $layout = $service == 'tama' ? "print-tama-services" : "print-calling-card";
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
                $checkInvoice = Invoice::where('month', $exploded_month[1])
                    ->where('year', $exploded_month[0])
                    ->where('user_id', $user)
                    ->where('service', 'tama1')
                    ->first();
					
                if (!$checkInvoice) {
                    //tama service invoice amount
                    $total_amount = User::select([
                        \DB::raw('sum(orders.order_amount) AS sale_price')
                    ])
                        ->join('orders', 'orders.user_id', '=', 'users.id')
                        ->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
                        ->where('orders.is_parent_order', 1)
                        ->where('orders.service_id', "!=", 7)
                        ->where('users.id', "=", $user)->first();
						

                    if ($total_amount->sale_price == null || $total_amount->sale_price == 0) {
                        continue;
                    }
                    //get last invoice number
                    $last_invoice_no = Invoice::where('month', $exploded_month[1])
                        ->where('year', $exploded_month[0])
                        ->select('count')->orderBy('id', "DESC")->first();

                    $last_invoice = isset($last_invoice_no) && $last_invoice_no->count != 0 ? $last_invoice_no->count + 1 : 1;

										
                    $invoice = new Invoice();
                    $invoice->user_id = $user;
                    $invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . "000" . $last_invoice;
                    $invoice->date = date("Y-m-d H:i:s");
                    $invoice->month = $exploded_month[1];
                    $invoice->year = $exploded_month[0];
                    $invoice->period = str_replace("00:00:00", "", $startDateMonth) . " au " . str_replace("23:59:59", "", $endDateMonth);
                    $invoice->period_start = $startDateMonth;
                    $invoice->period_end = $endDateMonth;
                    $invoice->total_amount = $total_amount->sale_price;
                    $invoice->commission_amount = 0;
                    $invoice->grand_total = 0;
                    $invoice->service = 'tama';
                    $invoice->count = $last_invoice;
                    $invoice->created_at = date("Y-m-d H:i:s");
                    $invoice->save();
                    $invoice_commission_total_amount = 0;
                    //calculate service commission
                    foreach ($user_access as $access) {
                        $service_total_amount = User::select([
                            \DB::raw('sum(orders.order_amount) AS sale_price')
                        ])
                            ->join('orders', 'orders.user_id', '=', 'users.id')
                            ->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
                            ->where('orders.is_parent_order', 1)
                            ->where('orders.service_id', "=", $access->id)
                            ->where('users.id', "=", $user)->first();
							
							
                        if ($service_total_amount->sale_price != null) {
                            $getUserCommission = ServiceHelper::get_service_commission($user, $access->id);
                            if($userInfo->group_id == 3){
                                $getUserCommission = 1;
                            }
                            Log::info("user commission $access->id $getUserCommission");
                            $serviceCommission = ServiceHelper::calculate_commission($service_total_amount->sale_price, $getUserCommission);
                            $subServiceCommission = $service_total_amount->sale_price - $serviceCommission;
                            Log::info("user service commission $serviceCommission $subServiceCommission");
							$r = [
									'user_id' => $user,
									'invoice_id' => $invoice->id,
									'service_id' => $access->id,
									'total_amount' => round($service_total_amount->sale_price, 2),
									'commission' => $getUserCommission,
									'commission_amount' => round($subServiceCommission, 2),
									'created_at' => now()
								];

                            $invoice_commission = new InvoiceCommission();
                            $invoice_commission->user_id = $user;
                            $invoice_commission->invoice_id = $invoice->id;
                            $invoice_commission->service_id = $access->id;
                            $invoice_commission->total_amount = number_format($service_total_amount->sale_price, 2);
                            $invoice_commission->commission = $getUserCommission;
                            $invoice_commission->commission_amount = number_format($subServiceCommission, 2);
                            $invoice_commission->created_at = date("Y-m-d H:i:s");
                            $invoice_commission->save();

                            $invoice_commission_total_amount += $subServiceCommission;
                        }
                    }
                    //update the invoice
                    Invoice::where('id', $invoice->id)->update([
                        'commission_amount' => number_format($invoice_commission_total_amount, 2),
                        'grand_total' => number_format($total_amount->sale_price - $invoice_commission_total_amount, 2)
                    ]);
                  
                    Log::info("invoice created for $userInfo->username");
                } else {
                    Log::info("Invoices already exists for the user $user");
                }
                //check for calling card
                $callingCardOrders = User::select([
                    \DB::raw('sum(orders.order_amount) AS sale_price')
                ])
                    ->join('orders', 'orders.user_id', '=', 'users.id')
                    ->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
                  ->where('orders.is_parent_order', 1)
                    ->where('orders.service_id', "=", 7)
                    ->where('users.id', "=", $user)->first();
                if ($callingCardOrders && $callingCardOrders->sale_price != null) {
                    $last_invoice_no = Invoice::where('month', $exploded_month[1])
                        ->where('year', $exploded_month[0])
                        ->select('count')->orderBy('id', "DESC")->first();
                    $last_invoice = $last_invoice_no->count + 1;
                    $invoice = new Invoice();
                    $invoice->user_id = $user;
                    $invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . "000" . $last_invoice;
                    $invoice->date = date("Y-m-d H:i:s");
                    $invoice->month = $exploded_month[1];
                    $invoice->year = $exploded_month[0];
                    $invoice->period = str_replace("00:00:00", "", $startDateMonth) . " au " . str_replace("23:59:59", "", $endDateMonth);
                    $invoice->period_start = $startDateMonth;
                    $invoice->period_end = $endDateMonth;
                    $invoice->total_amount = $callingCardOrders->sale_price;
                    $invoice->commission_amount = 0;
                   $invoice->grand_total = $callingCardOrders->sale_price;
                    $invoice->service = 'calling-card';
                    $invoice->count = $last_invoice;
                    $invoice->created_at = date("Y-m-d H:i:s");
                    $invoice->save();
                }
            }
        }
        return back()->with([
            'message' => "Invoices generated!",
            'message_type' => "success"
        ]);
    }

private function createRetailerInvoices($user, $exploded_month, $startDateMonth, $endDateMonth)
{
    //try {
        $retailers = User::where('id', $user)->get();
        foreach ($retailers as $retailer) {
            $retailer_user_access = Service::join('user_access', 'user_access.service_id', 'services.id')
                ->where('user_access.user_id', $retailer->id)
                ->where('user_access.status', 1)
                ->whereIn('services.id', [2, 7, 8, 9])  // Only fetch the relevant services
                ->select('services.*')
                ->get();
		
            if ($retailer_user_access->isEmpty()) {
                Log::warning("Retailer $retailer->username does not have access to any service");
                continue;
            }

            $checkRetailerInvoice = Invoice::where('month', $exploded_month[1])
                ->where('year', $exploded_month[0])
                ->where('user_id', $retailer->id)
                ->first();

          //  if ($checkRetailerInvoice) {
            //    continue;
            //}
            // Initialize the total amount for grouped services
            $tamaTopupTotalAmount = 0;
            $callingCardsTotalAmount = 0;
            $flixBusTotalAmount = 0;

	
            foreach ($retailer_user_access as $retailer_access) {
                $service_total_amount = User::join('orders', 'orders.user_id', 'users.id')
                    ->whereBetween('orders.date', [$startDateMonth, $endDateMonth])
                    ->where('orders.is_parent_order', 1)
                    ->where('orders.service_id', $retailer_access->id)
                    ->where('users.id', $retailer->id)
                    ->sum('orders.order_amount');
					$getRetailerCommission = ServiceHelper::get_service_commission($retailer, $retailer_access->id);
					dd($retailer_access);
					$serviceCommission = ServiceHelper::calculate_commission($service_total_amount, $getRetailerCommission);
					$subServiceCommission = $service_total_amount - $serviceCommission;
                if ($retailer_access->id == 2 || $retailer_access->id == 8) {
                    // Sum the amounts for Tama Topup and Tama Topup France
                    $tamaTopupTotalAmount += $service_total_amount;
                } elseif ($retailer_access->id == 7) {
                    // Sum the amounts for Calling Cards
                    $callingCardsTotalAmount += $service_total_amount;
                } elseif ($retailer_access->id == 9) {
                    // Sum the amounts for Flix Bus
                    $flixBusTotalAmount += $service_total_amount;
                } else {
                    // Process other services individually
                   // $this->createInvoice($retailer, $retailer_access, $exploded_month, $startDateMonth, $endDateMonth, $service_total_amount);
                }
            }
			
				$TopupService= Service::where('id', 2)->first();
				
                $k = self::createInvoice($retailer, $TopupService, $exploded_month, $startDateMonth, $endDateMonth, $tamaTopupTotalAmount, true, 'Tama Topup and Tama Topup France');
        dd($k);

            // Create invoice for Calling Cards
            if ($callingCardsTotalAmount > 0) {
                $callingCardsService = Service::where('id', 7)->first();
                //$this->createInvoice($retailer, $callingCardsService, $exploded_month, $startDateMonth, $endDateMonth, $callingCardsTotalAmount);
            }

            // Create invoice for Flix Bus
            if ($flixBusTotalAmount > 0) {
                $flixBusService = Service::where('id', 9)->first();
                //$this->createInvoice($retailer, $flixBusService, $exploded_month, $startDateMonth, $endDateMonth, $flixBusTotalAmount);
            }
        }
//    } catch (\Exception $e) {
  //      Log::error("Error in createRetailerInvoices: " . $e->getMessage());
    //}
}

private function createInvoice($retailer, $retailer_access, $exploded_month, $startDateMonth, $endDateMonth, $service_total_amount, $isGrouped , $groupName = '')
{

   $last_invoice_no = Invoice::where('month', $exploded_month[1])
        ->where('year', $exploded_month[0])
        ->orderBy('id', 'desc')
        ->first();
   $last_invoice = $last_invoice_no ? $last_invoice_no->count + 1 : 1;
    $ret_invoice = new Invoice();
    $ret_invoice->user_id = $retailer->id;
    $ret_invoice->invoice_ref = "INV" . $exploded_month[0] . $exploded_month[1] . str_pad($last_invoice, 4, '0', STR_PAD_LEFT);
    $ret_invoice->date = now();
    $ret_invoice->month = $exploded_month[1];
    $ret_invoice->year = $exploded_month[0];
    $ret_invoice->period = $startDateMonth->format('Y-m-d') . " au " . $endDateMonth->format('Y-m-d');
    $ret_invoice->period_start = $startDateMonth;
    $ret_invoice->period_end = $endDateMonth;
    $ret_invoice->total_amount = $service_total_amount;
    $ret_invoice->commission_amount = 0;
    $ret_invoice->grand_total = 0;
    $ret_invoice->service = $isGrouped ? $groupName : $retailer_access->name;
    $ret_invoice->count = $last_invoice;
    $ret_invoice->created_at = now();
    $ret_invoice->save();


$getRetailerCommission = 20;
$serviceCommission = ServiceHelper::calculate_commission($service_total_amount, $getRetailerCommission);
$subServiceCommission = $service_total_amount - $serviceCommission;

							
$r = [
    'user_id' => $retailer->id,
    'invoice_id' => $ret_invoice->id,
    'service_id' => $retailer_access->id,
    'total_amount' => round($service_total_amount, 2),
    'commission' => $getRetailerCommission,
    'commission_amount' => round($subServiceCommission, 2),
    'created_at' => now()
];

dd($r);
    if (!$isGrouped) {
        $ret_invoice_commission = new InvoiceCommission();
        $ret_invoice_commission->user_id = $retailer->id;
        $ret_invoice_commission->invoice_id = $ret_invoice->id;
        $ret_invoice_commission->service_id = $retailer_access->id;
        $ret_invoice_commission->total_amount = round($service_total_amount, 2);
        $ret_invoice_commission->commission = $getRetailerCommission;
        $ret_invoice_commission->commission_amount = round($subServiceCommission, 2);
        $ret_invoice_commission->created_at = now();
        $ret_invoice_commission->save();
		dd($ret_invoice_commission);
        $ret_invoice->update([
            'commission_amount' => round($subServiceCommission, 2),
            'grand_total' => round($service_total_amount - $subServiceCommission, 2)
        ]);
    }

    Log::info("Retailer invoice created for user $retailer->username for service " . ($isGrouped ? $groupName : $retailer_access->name));
}



    function viewInvoice($id, $service)
    {
        $invoice = Invoice::join('users', 'users.id', 'invoices.user_id')
            ->where('invoices.id', $id)
            ->where('service', $service)
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.address'
                , 'users.cust_id',  'invoices.*')
            ->first();
        if (!$invoice) {
            Log::warning('unable to find invoice for invoice id ' . $id);
            return back()->with([
                'message' => "Unable to download your invoice!",
                'message_type' => "warning"
            ]);
        }
        $layout = $service == 'tama' ? "print-tama-services" : "print-calling-card";
        $servicePrintData = InvoiceCommission::join('services', 'services.id', 'invoice_commissions.service_id')
            ->where('invoice_commissions.invoice_id', $invoice->id)
            ->select('services.name as service_name', 'invoice_commissions.*')
            ->get();

//        $layout = $service == 'tama' ? "print-tama-services" : "print-calling-card";
//        return view('app.invoices.'.$layout,[
//            'invoice' => $invoice,
//            'servicePrintData' => $servicePrintData
//        ]);
        return view('app.invoices.view', [
            'id' => $id,
            'service' => $service
        ]);
    }

    /**
     * Render Invoice
     * @param $id
     * @param $service
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
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
        $layout = $service == 'tama' ? "print-tama-services" : "print-calling-card";
        $servicePrintData = InvoiceCommission::join('services', 'services.id', 'invoice_commissions.service_id')
            ->where('invoice_commissions.invoice_id', $invoice->id)
            ->select('services.name as service_name', 'invoice_commissions.*')
            ->get();
        //dd($servicePrintData);
        $fileName = $invoice->username . " " . $invoice->invoice_ref;
        return PDF::loadView('app.invoices.' . $layout, [
            'invoice' => $invoice,
            'servicePrintData' => $servicePrintData
        ])->inline($fileName);
    }

    /**
     * Email invoice to user
     * @param $id
     * @param $service
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
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
        $layout = $service == 'tama' ? "print-tama-services" : "print-calling-card";
        $servicePrintData = InvoiceCommission::join('services', 'services.id', 'invoice_commissions.service_id')
            ->where('invoice_commissions.invoice_id', $invoice->id)
            ->select('services.name as service_name', 'invoice_commissions.*')
            ->get();
        $pdf = PDF::loadView('app.invoices.' . $layout, [
            'invoice' => $invoice,
            'servicePrintData' => $servicePrintData
        ]);
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

