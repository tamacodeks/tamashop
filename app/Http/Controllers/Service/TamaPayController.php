<?php

namespace App\Http\Controllers\Service;

use App\Events\TamaPayOrder;
use app\Library\AppHelper;
use app\Library\ServiceHelper;
use App\Models\AppCommission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TrackOrder;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Validator;

class TamaPayController extends Controller
{
    private $service_id;
    private $client;
    function __construct()
    {
        parent::__construct();
        $this->service_id = 4;
        $this->middleware(function ($request, $next) {
            if(API_TOKEN == '' || API_END_POINT == ''){
                AppHelper::logger('warning','API SETTINGS ERROR',"Missing API Token or API end point url",request()->all(),true);
                return redirect()->back()
                    ->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            if(AppHelper::user_access($this->service_id,auth()->user()->id) == 0){
                AppHelper::logger('warning','Access Violation',auth()->user()->username. " trying to access tamapay service",request()->all(),true);
                return redirect()->back()
                    ->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            //lets check with this user parent has access
            if(\app\Library\AppHelper::skip_service_as_menu('tama-pay') == false){
                AppHelper::logger('warning', 'Access Violation', auth()->user()->username . " trying to access TamaPay service but parent of this user does not have a access", request()->all());
                return redirect('dashboard')
                    ->with('message', trans('common.access_violation'))
                    ->with('message_type', 'warning');
            }
            $this->client = new Client([
                'base_uri' => API_END_POINT,
                'timeout'  => 120,
            ]);
            return $next($request);
        });
    }

    /**
     * View - Search Pin
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(Request $request)
    {
        $tama_pin= $request->input('tama_pin');
        $this->data['page_title'] = "Tama Pay :: Pay for your loved ones";
        $this->data['tama_pin'] = $tama_pin;
        $this->data['service_id'] = $this->service_id;
        if(!empty($tama_pin)){
            try{
                $response = $this->client->request('GET', 'tama-pay/'.$tama_pin,[
                    'headers'  => [
                        'Accept' => 'application/json',
                        'Authorization' => "Bearer ".API_TOKEN
                    ]
                ]);
                if($response->getStatusCode() == 200){
                    $data = json_decode((string)$response->getBody(),true);
                    if(isset($data['error'])){
                        AppHelper::logger('warning',"TamaPay API",'TamaPay API HTTP Status '.$response->getStatusCode(),$response,true);
                        return redirect('tama-pay')->with('message',trans('service.tamapay_no_order'))
                            ->with('message_type','information');
                    }
                    $this->data['order_data'] = $data['data'];
                    $this->data['order_json'] = $data['data']['tamapay_products'];
                    $this->data['order_ok'] = true;
                }else{
                    AppHelper::logger('warning',"TamaPay API",'TamaPay API HTTP Status '.$response->getStatusCode(),$response,true);
                    return redirect()->back()->with('message',trans('service.service_unavailable'))
                        ->with('message_type','warning');
                }
            }catch (\Exception $e){
                AppHelper::logger('warning',"TamaPay API HTTP Exception",$e->getMessage(),$e,true);
                return redirect()->back()->with('message',trans('service.service_unavailable'))
                    ->with('message_type','warning');
            }
        }else{
            $this->data['order_data'] = false;
            $this->data['order_json'] = false;
            $this->data['order_ok'] = false;
        }
        return view('service.tama-pay.index',$this->data);
    }

    /**
     * POST Confirm Order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function confirm_order(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'order_pin' => "required|min:9|max:9",
            'order_id' => "required",
            'order_amount' => "required",
        ]);
        if($validator->fails()){
            $html = AppHelper::create_error_bag($validator);
            AppHelper::logger('warning','TamaFamily Confirm Order Validation Failed',$html,$request->all());
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        $order_pin = $request->input('order_pin');
        $transID = "TP".date("y") . strtoupper(date('M')) . date('d') . date('His');
        $user_info = User::find(auth()->user()->id);
        $order_total = $request->order_amount;
        //lets check the parent balance or credit limit with in the order amount
        if (ServiceHelper::parent_rule_check($user_info->parent_id, $order_total,$this->service_id)) {
            //parent does not have enough money or credit limit
            //order will be failed
            AppHelper::logger('warning', 'Parent Rule Failed', $user_info->username . ' parent does not have enough balance or credit limit to confirm tama topup order', $request->all());
            Log::warning('TamaTopup Parent Rule Failed => ' . $user_info->username . ' => ' . $user_info->parent_id);
            return redirect()->back()
                ->with('message', trans('common.parent_rule_failed'))
                ->with('message_type', 'warning');
        }
        //check with user balance and credit limit
        $current_balance = AppHelper::getBalance($user_info->id, $user_info->currency, false);
        $user_service_commission = ServiceHelper::get_service_commission($user_info->id, $this->service_id);//service_id may change
        $order_amount = ServiceHelper::calculate_commission($order_total, $user_service_commission);
        $user_credit_limit = AppHelper::get_credit_limit($user_info->id);
        $sale_margin = ServiceHelper::calculate_sale_margin($order_total, $order_amount);
        if ($current_balance < $order_amount) {
            //check with credit limit
            if (ServiceHelper::check_with_credit_limit($order_amount, $current_balance, $user_credit_limit) == false) {
                AppHelper::logger('warning', 'TamaPay Balance Error', auth()->user()->username . ' does not have enough balance or credit limit to confirm tamapay order', $request->all());
                return redirect()->back()
                    ->with('message', trans('common.msg_order_failed_due_bal'))
                    ->with('message_type', 'warning');
            }
        }
        $response_data= [];
        try{
            //tama service confirm order api call
            $response = $this->client->request('POST', 'tama-pay/confirm', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ],
                'form_params' => [
                    'tama_pin' => $request->order_pin, //required
                    'order_id' => $request->order_id, //required
                    'TransID' => $transID
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $response_api= json_decode((string)$response->getBody(), true);
                $response_data = $response_api['data'];
                $track_order_id = TrackOrder::insertGetId([
                    'trans_id' => $transID,
                    'user_id' => $user_info->id,
                    'api_order_id' => $response_data['order_id'],
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth()->user()->id,
                    'remarks' => "Order Transaction is about to initiate..."
                ]);
            }
        }catch (BadResponseException $e){
            $response = $e->getResponse();
            $responseBodyAsString =  json_decode((string)$response->getBody()->getContents(),true);
            AppHelper::logger('warning',"TamaPay API",'TamaPay API HTTP Status '.$e->getCode(),$responseBodyAsString,true);
            return redirect()->back()->with('message',trans('common.msg_order_failed'))
                ->with('message_type','warning');
        }
        try {
            $getOrderInfos = $response_data;
            $sender_name_to = $getOrderInfos['sender_name'];
            $sender_mobile_to = $getOrderInfos['sender_mobile'];
            $receiver_name_to = $getOrderInfos['receiver_name'];
            $receiver_mobile_to = $getOrderInfos['receiver_mobile'];
            $public_price = $getOrderInfos['order_amount'];
            $order_comment = $getOrderInfos['order_note'];
            $tama_pin = $getOrderInfos['tama_pin'];
            $order_status = 6;
            $checkJson = $getOrderInfos['tamapay_products'];
            \DB::beginTransaction();
            $after_order_balance = number_format((float)$current_balance - $order_amount, 2, '.', '');
            $backup_comment = $user_info->username . ' confirm the tamapay(' . $order_pin . ') order in total of ' . $public_price;
            $order_desc = !empty($order_comment) ? $order_comment : $backup_comment;
            $txn_ref = TRANSACTION_PREFIX . ServiceHelper::genTransID(5);
            $created_at = date("Y-m-d H:i:s");

            //make transaction
            $trans_id = ServiceHelper::sync_transaction($user_info->id, $created_at, 'debit', $order_amount, $current_balance, $after_order_balance, $order_desc);
            //make order
            $order_id = Order::insertGetId([
                'date' => $created_at,
                'user_id' => $user_info->id,
                'service_id' => $this->service_id,
                'order_status_id' => 6, //payment received
                'transaction_id' => $trans_id,
                'txn_ref' => $txn_ref,
                'comment' => $order_desc,
                'currency' => $user_info->currency,
                'public_price' => $order_total,
                'sale_margin' => $sale_margin,
                'buying_price' => $order_amount,
                'order_amount' => $order_amount,
                'grand_total' => $order_amount,
                'created_at' => $created_at,
                'created_by' => $user_info->id,
            ]);
            //insert order items
            $order_item_id = OrderItem::insertGetId([
                'order_id' => $order_id,
                'sender_first_name' => $sender_name_to,
                'sender_mobile' => $sender_mobile_to,
                'receiver_first_name' => $receiver_name_to,
                'receiver_mobile' => $receiver_mobile_to,
                'tama_pin' => $order_pin,
                'created_at' => $created_at,
                'created_by' => $user_info->id,
            ]);
            //update the order item id to order
            Order::where('id',$order_id)->update([
                'order_item_id' => $order_item_id
            ]);
            $parent_user = User::find($user_info->parent_id);
            if (!empty($user_info->parent_id) && $parent_user && $parent_user->group_id != 2) {
                $parent_user_commission = ServiceHelper::get_service_commission($parent_user->id, $this->service_id);
                $parent_current_balance = AppHelper::getBalance($parent_user->id, $parent_user->currency, false);
                $parent_actual_commission = $parent_user_commission - $user_service_commission;
                $buying_price_parent = ServiceHelper::calculate_commission($order_total, $parent_user_commission);
                $order_amount_parent = ServiceHelper::calculate_commission($order_total, $parent_actual_commission);
                $parent_sale_margin = ServiceHelper::calculate_sale_margin($order_amount, $buying_price_parent);
                $parent_after_order_balance = number_format((float)$parent_current_balance - $buying_price_parent, 2, '.', '');
                //make transaction for parent
                $parent_trans_id = ServiceHelper::sync_transaction($parent_user->id, $created_at, 'debit', $buying_price_parent, $parent_current_balance, $parent_after_order_balance, $order_desc);
                //parent order insertion
                $parent_order_id = Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $user_info->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 6, //payment received
                    'transaction_id' => $parent_trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $order_total,
                    'buying_price' => $buying_price_parent,
                    'sale_margin' => $parent_sale_margin,
                    'order_amount' => $order_amount,
                    'grand_total' => $order_amount,
                    'is_parent_order' => 1,
                    'order_item_id' => $order_item_id,
                    'created_at' => $created_at,
                    'created_by' => $user_info->id,
                ]);

                //use the app commission to update order buying_price
                $app_commission = optional(AppCommission::where('service_id', $this->service_id)->first())->commission;
                $app_actual_commission = $app_commission - $parent_user_commission;
                $buying_price_app = ServiceHelper::calculate_commission($order_total, $app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($order_total, $app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($buying_price_parent, $buying_price_app);
                Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $parent_user->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 6, //payment received
                    'transaction_id' => $trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $order_total,
                    'buying_price' => $buying_price_app,
                    'sale_margin' => $app_sale_margin,
                    'order_amount' => $buying_price_parent,
                    'grand_total' => $buying_price_parent,
                    'is_parent_order' => 1,
                    'exclude' => 1,
                    'order_item_id' => $order_item_id,
                    'created_at' => $created_at,
                    'created_by' => $user_info->id,
                ]);
            } else {
                //use the app commission to update order buying_price
                $app_commission = optional(AppCommission::where('service_id', $this->service_id)->first())->commission;
                $app_actual_commission = $app_commission - $user_service_commission;
                $buying_price_app = ServiceHelper::calculate_commission($order_total, $app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($order_total, $app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($order_total, $order_amount_app);
                Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $user_info->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 6, //payment received
                    'transaction_id' => $trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $order_total,
                    'buying_price' => $buying_price_app,
                    'sale_margin' => $app_sale_margin,
                    'order_amount' => $order_amount,
                    'grand_total' => $order_amount,
                    'is_parent_order' => 1,
                    'order_item_id' => $order_item_id,
                    'created_at' => $created_at,
                    'created_by' => $user_info->id,
                ]);
            }
            TrackOrder::where('id', $track_order_id)->update([
                'order_id' => $order_id,
                'order_status_id' => 1,
                'status' => 1,
                'remarks' => "Tama Pay Order placed successfully!",
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id
            ]);
            \DB::commit();
            AppHelper::logger('success', 'TamaPay Order #' . $order_id, $order_desc);
            $url = auth()->user()->group_id == 4 ? "orders" : "my/orders";
            return redirect($url)->with('message', trans('service.tama_order_placed_suc_callback'))->with('message_type', 'success');
        }
        catch (\Exception $e) {
            \DB::rollback();
            $exception_id = 'TPEX' . AppHelper::Numeric(5);//to know more about exception
            //change the order status
            $rollback_order= TrackOrder::where('trans_id', $transID)->update([
                'status' => 0,
                'remarks' => "Unable to place order, Exception occur => ".$exception_id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id
            ]);
            if($rollback_order){
                ServiceHelper::rollBackOrder($transID);
            }
            $exceptions = [
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Code' => $e->getCode()
            ];
            Log::emergency(auth()->user()->username . " TamaPay API Exception => " . $e->getMessage());
            AppHelper::logger('warning', 'TamaPay Exception ' . $exception_id, $e->getMessage(),$exceptions);
            return redirect()->back()
                ->with('message', trans('common.error_confirm_order') . ' ' . $exception_id)
                ->with('message_type', 'warning');
        }

    }
}
