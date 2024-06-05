<?php

namespace App\Http\Controllers\App;

use App\Events\PaymentReceived;
use app\Library\AppHelper;
use App\Models\Payment;
use App\Models\Transaction;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Stripe\Stripe;
use App\Models\DailyLimit;

class PaymentController extends Controller
{
    private $client;
    function __construct()
    {
        parent::__construct();
        $this->client = new Client([
            'base_uri' => API_END_POINT,
            'timeout' => 5.0,
        ]);
    }

    /**
     * View All Payments
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $retailer_qry = User::where('status',1);
        if(in_array(auth()->user()->group_id,[1,2,3])){
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $retailer_qry->whereIn('id',$retailers);
            $retailers = $retailer_qry->select('id','username')->get();
        }else{
            $retailers = [];
        }
        // For today
                $daily_transaction = Transaction::where('type', 'credit')
                    ->where('user_id', auth()->user()->id)
                    ->whereBetween('date', [
                        now()->startOfDay(),
                        now()->endOfDay(),
                    ])->sum('amount');

        // For the past week
                $weekly_transaction = Transaction::where('type', 'credit')
                    ->where('user_id', auth()->user()->id)
                    ->whereBetween('date', [
                        now()->subWeek()->startOfDay(),
                        now()->endOfDay(),
                    ])->sum('amount');

        // For the past month
                $monthly_transaction = Transaction::where('type', 'credit')
                    ->where('user_id', auth()->user()->id)
                    ->whereBetween('date', [
                        now()->subMonth()->startOfDay(),
                        now()->endOfDay(),
                    ])->sum('amount');
        $page_data = [
            'page_title' => trans('common.my_payments'),
            'retailers' => $retailers,
            'remaining_daily' => max(0, auth()->user()->daily - $daily_transaction),
            'remaining_monthly' => max(0, auth()->user()->monthly - $monthly_transaction),
            'remaining_weekly' => max(0, auth()->user()->weekly - $weekly_transaction),
        ];
        return view('app.payments.index',$page_data);
    }

    /**
     * Ajax Get All Payments
     * @param Request $request
     * @return mixed
     */
    function getPayments(Request $request)
    {
        $query = Payment::leftjoin('transactions','transactions.id','payments.transaction_id')
            ->join('users','users.id','payments.user_id')
            ->select([
                'payments.date',
                'transactions.date as payment_date',
                'users.username',
                'users.cust_id',
                'payments.amount',
                'transactions.prev_bal',
                'transactions.balance',
                'payments.description',
                'payments.received_by',
                'transactions.created_at'
            ]);
        if(auth()->user()->group_id == 2){
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
//            $to_retrieve = array_add($retailers,count($retailers),auth()->user()->id);
            $query->whereIn('payments.user_id',$retailers);
        }elseif(auth()->user()->group_id == 3){
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $to_retrieve = array_add($retailers,count($retailers),auth()->user()->id);
            $query->whereIn('payments.user_id',$retailers);
        }elseif(auth()->user()->group_id == 4){
            $query->whereIn('payments.user_id',[auth()->user()->id]);
        }else{
            $query->where(function ($query) {
                $query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            });
        }
        $query->orderBy('created_at', 'DESC');
        $payments = $query;
        return Datatables::of($payments)
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('retailer_id'))) {
                    $query->whereIn('users.id',$request->input('retailer_id'));
                }
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date').' 00:00:00';
                    $to_date = $request->input('to_date').' 23:59:59';
                    $query->whereBetween('payments.date',[$from_date,$to_date]);
                }
            })
            ->make(true);

    }

    /**
     * View Add New payment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function add_payment()
    {
        $retailer_qry = User::join('transactions','transactions.user_id','users.id')
            ->where('status',1);
        if(in_array(auth()->user()->group_id,[1,2])){
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $retailer_qry->whereIn('users.id',$retailers);
        }else{
            $child = User::where('parent_id',auth()->user()->id)->get();
            $retailers = $child->pluck('id')->flatten()->toArray();
            $retailer_qry->whereIn('users.id',$retailers);
        }
        $retailer_qry->whereNotIn('users.group_id',[1,2,6]); // to filter administrators
        $retailer_qry->whereRaw('transactions.id = (select max(`id`) from transactions where transactions.user_id = users.id)');
        $users = $retailer_qry->select('users.id','users.username','users.currency','transactions.balance')->get();
//        dd($users);
        $payments = Payment::join('transactions','transactions.id','payments.transaction_id')
            ->join('users','users.id','payments.user_id')
            ->select([
                'payments.date',
                'users.id',
                'users.username',
                'users.cust_id',
                'payments.amount',
                'transactions.prev_bal',
                'transactions.balance',
                'payments.description',
                'payments.received_by'
            ])->whereIn('users.id',$retailers)->orderBy('payments.id',"DESC")->whereBetween('payments.date',[date('Y-m-d')." 00:00:00",date('Y-m-d')." 23:59:59"])->take(20)->get();
        $intiated_payment = Payment::join('users', 'users.id', 'payments.user_id')
            ->select([
                'payments.date',
                'users.id',
                'users.username',
                'users.cust_id',
                'payments.amount',
                'payments.description',
                'payments.received_by'
            ])->Where('payments.transaction_id', NULL)
            ->whereIn('users.id', $retailers)->orderBy('payments.id', "DESC")
            ->whereBetween('payments.date', [date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59"])
            ->take(20)->get();

        $page_data = [
            'page_title' => trans('common.update_payment'),
            'retailers' => $users,
            'payments' => $payments,
            'intiated_payment' => $intiated_payment
        ];
        return view('app.payments.update', $page_data);
    }

    /**
     * POST update payment
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function update_payment(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'retailer_id' => 'required',
            'amount' => 'required'
        ]);
        if ($validator->fails()) {
            AppHelper::logger('warning','Payment Update Validation','Validation failed',$request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        $user = User::find($request->retailer_id);
        $old_user_balance = AppHelper::getBalance($user->id,$user->currency,false);
        $payment_id = Payment::insertGetId([
            'user_id' => $user->id,
            'transaction_id' => NULL,
            'date' => date('Y-m-d H:i:s'),
            'amount' => $request->amount,
            'description' => $request->description,
            'received_by' => auth()->user()->id
        ]);
        $payment = Payment::find($payment_id);
        event(new PaymentReceived($payment));
        $emails = explode(',',PAYMENT_EMAILS);
        $send_email_order_data = array(
            'updater' => auth()->user()->username,
            'amount' => $request->amount,
            'reseller_name' => $user->username,
            'desc' => $request->description
        );
        \Mail::send('emails.payment_added', $send_email_order_data, function ($message) use ($emails) {
            $message->from('noreply@tamaexpress.com', 'Tama Retailer');
            $message->to($emails)->subject('Payment Added');
        });
        AppHelper::logger('success', 'Payment Update', $user->username . ' payment was updated by ' . auth()->user()->username);
        return redirect()->back()
            ->with('message',trans('common.payment_updated_message'))
            ->with('message_type','success');
    }


    /**
     * View My Payments
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function myPayments()
    {
        $page_data = [
            'page_title' => trans('common.my_payments')
        ];
        if(auth()->user()->group_id == 2){
            try{
                $response = $this->client->request('GET', 'payments', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => "Bearer " . API_TOKEN
                    ]
                ]);
                if ($response->getStatusCode() == 200) {
                    $data = json_decode((string)$response->getBody(), true);
//                    dd($data);
                    $page_data['payments']  = $data['data'];
                    $page_data['payments_paginator']  = $data['meta'];
                } else {
                    Log::warning('MY PAYMENTS API HTTP Status ' . $response->getStatusCode());
                    $page_data['payments'] = [];
                }
            }catch (\Exception $e){
                Log::warning('MY PAYMENTS API HTTP Exception ' . $e->getMessage());
                $page_data['payments'] = [];
            }
        }
//        dd($page_data);
        return view('app.payments.mypayments',$page_data);
    }

    /**
     * Ajax Get All My Payments
     * @param Request $request
     * @return mixed
     */
    function getMyPayments(Request $request)
    {
        $query = Payment::join('transactions','transactions.id','payments.transaction_id')
            ->join('users','users.id','payments.user_id')
            ->select([
                'payments.date',
                'users.username',
                'users.cust_id',
                'payments.amount',
                'transactions.prev_bal',
                'transactions.balance',
                'payments.description',
                'payments.received_by'
            ]);
        $query->where('payments.user_id',auth()->user()->id);
        $payments = $query;
        return Datatables::of($payments)
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('retailer_id'))) {
                    $query->whereIn('users.id',$request->input('retailer_id'));
                }
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date').' 00:00:00';
                    $to_date = $request->input('to_date').' 23:59:59';
                    $query->whereBetween('payments.date',[$from_date,$to_date]);
                }
            })
            ->make(true);

    }
    function add_limit()
    {
        $retailer_qry = User::where('users.status',1);
        if(auth()->user()->group_id == 3){
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $retailer_qry->whereIn('users.id',$retailers);
        }else{
            $retailer_qry->where(function ($query) {
                $query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            });
            $child = User::where('users.parent_id', '=', '0')
                ->orWhereNull('users.parent_id')->get();
            $retailers = $child->pluck('id')->flatten()->toArray();
        }
        $retailer_qry->whereNotIn('users.group_id',[1,2,6]); // to filter administrators
        $users = $retailer_qry->select('users.id','users.username','users.currency')->get();
//        dd($users);

        $page_data = [
            'page_title' => trans('common.update_limit'),
            'retailers' => $users,
        ];
        return view('app.limits.update',$page_data);
    }
    function update_limit(Request $request)
    {
        $from = date("Y-m-d  00:00:00.000000'");
        $to = date("Y-m-d  23:59:59.999999'");
        $result = \App\Models\Transaction::where('user_id', $request->retailer_id)->where('type','debit')->whereBetween('created_at', [$from, $to])->orderBy('id', 'DESC')->get();
//        dd($result->sum('amount'));
        if($result->sum('amount') >= $request->amount)
        {
            AppHelper::logger('warning','Low Limit Problem','Limit failed',$request->all());
            return redirect()->back()
                ->with('message',trans('common.msg_update_error'))
                ->with('message_type','warning');
        }
        $min_limit =$request->current_balance + $request->remaining_bal;
        if($min_limit >= $request->amount)
        {
            AppHelper::logger('warning','Low Limit Problem','Limit failed',$request->all());
            return redirect()->back()
                ->with('message',trans('common.msg_update_error'))
                ->with('message_type','warning');
        }
        $user = User::find($request->retailer_id);
        $emails = explode(',',PAYMENT_EMAILS);
        $send_email_order_data = array(
            'updater' => auth()->user()->username,
            'amount' => $request->amount,
            'reseller_name' => $user->username,
            'current_balance' => $request->current_balance,
            'remaining_bal' => $request->remaining_bal,
        );

        $validator = Validator::make($request->all(),[
            'retailer_id' => 'required',
            'amount' => 'required'
        ]);
        if ($validator->fails()) {
            AppHelper::logger('warning','Limit Update Validation','Validation failed',$request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        $user = User::find($request->retailer_id);
        try{
            \DB::beginTransaction();
            $daily_limit = DailyLimit::where('user_id', $request->retailer_id)->first();
            if (!empty($daily_limit)) {
                DailyLimit::where('id', $daily_limit->id)->where('user_id', $request->retailer_id)->update([
                    'daily_limit' => $request->amount,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => auth()->user()->id
                ]);
            } else {
                //insert as new
                DailyLimit::insert([
                    'type' => 'credit',
                    'user_id' => $request->retailer_id,
                    'daily_limit' =>  $request->amount,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_by' => auth()->user()->id
                ]);
            }
            \DB::commit();
            \Mail::send('emails.daily_limit_update', $send_email_order_data, function ($message) use ($emails) {
                $message->from('noreply@tamaexpress.com', 'Tama Retailer');
                $message->to($emails)->subject('Updated Daily Limit');
            });
            AppHelper::logger('success','Limit Update',$user->username.' Limit was updated by '.auth()->user()->username);
            return redirect()->back()
                ->with('message',trans('common.limit_update_message'))
                ->with('message_type','success');
        }catch (\Exception $e){
            AppHelper::logger('warning','Limit Update Exception',$e->getMessage());
            return redirect()->back()
                ->with('message',trans('common.msg_update_error'))
                ->with('message_type','warning');
        }
    }

    function delete_limit(Request $request)
    {
//        dd($request->id_retailer);
        $post =DailyLimit::where('user_id',$request->id_retailer)->first();
        $user =User::where('id',$request->id_retailer)->first();
        if ($post != null) {
            $post->delete();
            AppHelper::logger('success','Limit Deleted',$user->username.' Limit was Deleted by '.auth()->user()->username);
            return redirect()->back()
                ->with('message','Limit was removed')
                ->with('message_type','success');
        }
        AppHelper::logger('warning','Limit Not Set For This User',$user->username);
        return redirect()->back()
            ->with('message','Limit Not Set For This User')
            ->with('message_type','success');

    }
    public function stripePost(Request $request)
    {
//        dd($request->all());
        Stripe::setApiKey(STRIPE_SECRET);
        $intent = null;
//        try {
            if (isset($request->payment_method_id)) {
                $user = User::find($request->user_id);
                $old_user_balance = AppHelper::getBalance($user->id, $user->currency, false);
                $description = 'Payment Done From Stripe By' . ' '.$user->username.' From Tamashop';
                $user_data = ([
                    'user_id' => $request->user_id,
                    'username' => $user->username,
                    'amount' => $request->amount,
                    'old_balance' => $old_user_balance,
                    'description' => $description,
                ]);
                $intent = \Stripe\PaymentIntent::create([
                    'amount' => $request->amount * 100,
                    'currency' => 'eur',
                    'payment_method_types' => ['card'],
                    'payment_method' => $request->payment_method_id,
                    'confirmation_method' => 'automatic', // Set confirmation_method to automatic
                    'confirm' => true,
                    'use_stripe_sdk' => true,
                    "description" => $description,
                ]);
              return self::generateResponse($intent, $user_data, 'payment_method');
            }
            if (isset($request->payment_intent_id)) {
                $user_data = $request->user_id;
                $intent = \Stripe\PaymentIntent::retrieve(
                    $request->payment_intent_id
                );
                $intent->confirm();
                return self::generateResponse($intent, $user_data, 'payment_intent');
            }

//        } catch (\Stripe\Exception\ApiErrorException $e) {
//            Log::insert([
//                'user_id' => $request->user_id,
//                'type' => 'warning',
//                'title' => 'Payment Update Exception',
//                'description' => 'Failed Payment' . $e,
//                'uri' => request()->getUri(),
//                'request_info' => json_encode($e->getMessage()),
//                'created_at' => date('Y-m-d H:i:s'),
//                'created_by' => $request->user_id
//            ]);
//            $emails = explode(',', PAYMENT_EMAILS);
//            $send_email_order_data = array(
//                'amount' => $request->amount,
//                'reseller_name' => $user->username,
//                'desc' => $e,
//                'status' => 0
//            );
//            \Mail::send('emails.payment_stripe_error', $send_email_order_data, function ($message) use ($emails) {
//                $message->from('noreply@tamaexpress.com', 'Tama Retailer');
//                $message->to($emails)->subject('Payment Added');
//            });
//            echo json_encode([
//                'title' => 'Error',
//                'message' => $e->getMessage()
//            ]);
//        }
    }
    function generateResponse($intent, $user_data, $method)
    {
        $user = User::find($user_data['user_id']);
        $old_user_balance = AppHelper::getBalance($user->id, $user->currency, false);
        $description = 'Payment Done From Stripe By' . ' '.$user->username.' From Tamashop';
        if ($intent->status == 'requires_action') {
            # Tell the client to handle the action
            return response()->json([
                'requires_action' => true,
                'payment_intent_client_secret' => $intent->client_secret
            ]);
        } else if ($intent->status == 'succeeded') {
            try {
                if ($method == 'payment_intent') {
                    $trans_id = Transaction::insertGetId([
                        'user_id' => $user->id,
                        'date' => date('Y-m-d H:i:s'),
                        'type' => 'credit',
                        'amount' => $intent->amount / 100,
                        'credit' => $intent->amount / 100,
                        'prev_bal' => $old_user_balance,
                        'balance' => $old_user_balance + ($intent->amount / 100),
                        'description' => $description,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user->id,
                    ]);

                    $payment_id = Payment::insertGetId([
                        'user_id' =>  $user->id,
                        'transaction_id' => $trans_id,
                        'date' => date('Y-m-d H:i:s'),
                        'amount' => $intent->amount / 100,
                        'description' => $description,
                        'received_by' => $user->id,
                    ]);
                    \App\Models\Log::insert([
                        'user_id' => $user->id,
                        'type' => 'success',
                        'title' => 'Payment Done By Stripe',
                        'description' => $description,
                        'uri' => request()->getUri(),
                        'request_info' => json_encode($intent),
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user->id
                    ]);
                } else {
                    $trans_id = Transaction::insertGetId([
                        'user_id' => $user_data['user_id'],
                        'date' => date('Y-m-d H:i:s'),
                        'type' => 'credit',
                        'amount' => $user_data['amount'],
                        'credit' => $user_data['amount'],
                        'prev_bal' => $user_data['old_balance'],
                        'balance' => $user_data['old_balance'] + $user_data['amount'],
                        'description' => $description,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user_data['user_id']
                    ]);
                    $payment_id = Payment::insertGetId([
                        'user_id' => $user_data['user_id'],
                        'transaction_id' => $trans_id,
                        'date' => date('Y-m-d H:i:s'),
                        'amount' => $user_data['amount'],
                        'description' => $user_data['description'],
                        'received_by' => $user_data['user_id']
                    ]);
                    \App\Models\Log::insert([
                        'user_id' => $user_data['user_id'],
                        'type' => 'success',
                        'title' => 'Payment Done By Stripe',
                        'description' => $user_data['description'],
                        'uri' => request()->getUri(),
                        'request_info' => json_encode($intent),
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_data['user_id']
                    ]);
                }
                return response()->json([
                    'title' => 'success',
                    "success" => true,
                    'message' => 'Payment Has Received Successfully'
                ]);
            } catch (Exception $e) {
                \App\Models\Log::insert([
                    'user_id' => $user_data['user_id'],
                    'type' => 'success',
                    'title' => 'Payment Done By Stripe',
                    'description' => $user_data['description'],
                    'uri' => request()->getUri(),
                    'request_info' => json_encode($intent),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_data['user_id']
                ]);
                return response()->json([
                    'title' => 'Error',
                    "success" => true,
                    'message' => 'Error Please Contact Manager'
                ]);
            }
        } else {
            # Invalid status
           # http_response_code(200);
            return response()->json([
                'title' => 'Error2',
                'error' => 'Invalid PaymentIntent status',
                'message' => 'Invalid PaymentIntent status'
            ]);
        }
    }
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(STRIPE_SECRET);
        try {
            // Find the user by ID
            $user = User::find($request->user_id);

            // Construct payment description
            $description = 'Payment Done From Stripe By ' . $user->username;

            // Create PaymentIntent
            $intent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Amount in cents
                'currency' => 'eur',
                'payment_method_types' => ['card'],
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'automatic', // Set confirmation_method to automatic
                'confirm' => true,
                'description' => $description,
            ]);

            // If the PaymentIntent requires a payment method action, return the status and client secret
            if ($intent->status === 'requires_action' && $intent->next_action->type === 'use_stripe_sdk') {
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $intent->client_secret
                ]);
            } else {
                // Otherwise, return the client secret directly
                return response()->json(['status' => $intent->status]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function retrievePaymentDetails(Request $request)
    {
        Stripe::setApiKey(STRIPE_SECRET);

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            $paymentDetails = [
                'id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                // Add more payment details as needed
            ];

            return response()->json(['status' => 'success', 'payment_details' => $paymentDetails]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function savePaymentDetails(Request $request)
    {
        // Validate the incoming request data if necessary

        // Extract payment details from the request
        $paymentDetails = $request->all();
        $user = User::find($paymentDetails['user_id']);
        $old_user_balance = AppHelper::getBalance($user->id, $user->currency, false);
        $description = 'Payment Done From Stripe By' . ' '.$user->username.' From Tamashop';



        $trans_id = Transaction::insertGetId([
            'user_id' => $user->id,
            'date' => date('Y-m-d H:i:s'),
            'type' => 'credit',
            'amount' => $paymentDetails['amount'],
            'credit' => $paymentDetails['amount'],
            'prev_bal' => $old_user_balance,
            'balance' => $old_user_balance + ($paymentDetails['amount']),
            'description' => $description,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'created_by' => $user->id,
        ]);

        $payment_id = Payment::insertGetId([
            'user_id' => $user->id,
            'transaction_id' => $trans_id,
            'date' => date('Y-m-d H:i:s'),
            'amount' => $paymentDetails['amount'],
            'description' => $description,
            'received_by' => $user->id,
        ]);
        \App\Models\Log::insert([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Payment Done By Stripe',
            'description' => $description,
            'uri' => request()->getUri(),
            'request_info' => json_encode($paymentDetails),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $user->id
        ]);
        // Optionally, return a response indicating success or failure
        return response()->json(['success' => true, 'message' => 'Payment details saved successfully']);
    }

}