<?php

namespace App\Http\Controllers\Service;

use App\Events\TamaFamilyOrder;
use app\Library\ApiHelper;
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

class TamaFamilyController extends Controller
{
    private $service_id;
    private $client;
    function __construct()
    {
        parent::__construct();
        $this->service_id = 6;
        $this->middleware(function ($request, $next) {
            if(API_TOKEN == '' || API_END_POINT == ''){
                AppHelper::logger('warning','API SETTINGS ERROR',"Missing API Token or API end point url",request()->all(),true);
                return redirect()->back()
                    ->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            if(AppHelper::user_access($this->service_id,auth()->user()->id) == 0){
                AppHelper::logger('warning','Access Violation',auth()->user()->username. " trying to access tamaapp service",request()->all(),true);
                return redirect()->back()
                    ->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            //lets check with this user parent has access
            if(\app\Library\AppHelper::skip_service_as_menu('tama-family') == false){
                AppHelper::logger('warning', 'Access Violation', auth()->user()->username . " trying to access TamaFamily service but parent of this user does not have a access", request()->all());
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

    function index()
    {
        $this->data = [
            'page_title' => "TamaFamily Recharge"
        ];
        return view('service.tama-family.index',$this->data);
    }

    /**
     * Ajax - Get User Balance
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function get_user_balance(Request $request){
        if ($request->ajax() == false) {
            AppHelper::logger('warning', 'TamaFamily Fetch Balance', "Trying access directly!", $request->all());
            return ApiHelper::response('400', '200', trans('common.access_violation'));
        }
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'dial_code' => 'required'
        ]);
        if ($validator->fails()) {
            AppHelper::logger('warning', 'TamaFamily Fetch Balance', "Validation Failed", $request->all());
            return ApiHelper::response('400', '200', trans('common.msg_fill_fields'));
        }
        try{
            $response = $this->client->request('POST', 'mytama/balance',[
                'headers'  => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer ".API_TOKEN
                ],
                'form_params' => [
                    'mobile_number' => $request->mobile_number,
                    'dial_code' => $request->dial_code,
                ]
            ]);
            if($response->getStatusCode() == 200){
                $result = json_decode((string)$response->getBody(),true);
                if(isset($result['error'])){
                    AppHelper::logger('warning', 'TamaFamily Fetch Balance', "Validation Failed", $request->all());
                    return ApiHelper::response('400', '200', trans('service.not_a_tamafamily_user'));
                }
                $ret_data = array(
                    'account_bal' => $result['data']['result']['account_bal']
                );
                AppHelper::logger('success', 'TamaFamily Balance Ajax', 'Balance Found for user ' . $request->mobile_number, $request->all());
                return AppHelper::response(200, 200, "Balance Found!", $ret_data);
            }else{
                AppHelper::logger('warning', 'TamaFamily Fetch Balance', "Validation Failed", $request->all());
                return ApiHelper::response('400', '200', trans('service.not_tamaapp_user'));
            }
        }catch (\Exception $e){
            AppHelper::logger('warning', 'TamaFamily HTTP Exception', $e->getMessage(), '',true);
            return ApiHelper::response('400', '200', trans('service.service_unavailable'));
        }

    }

    /**
     * Confirm TamaFamily Order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function confirm_order(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'mobile' => 'required',
            'areaCode' => 'required',
            'amount' => 'required'
        ]);
        if($validator->fails()){
            $html = AppHelper::create_error_bag($validator);
            AppHelper::logger('warning','TamaFamily Confirm Order Validation Failed',$html,$request->all());
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        $to_replace = "(+".$request->areaCode.")";
        $mobile_number = str_replace($to_replace,'',$request->mobile);
        $euro_amount = $request->amount;
        $user_info = User::find(auth()->user()->id);
        $order_comment = $user_info->username . " topup tamafamily account " . $mobile_number . " for " . $euro_amount;
        //lets check the parent balance or credit limit with in the order amount
        $check_limit = AppHelper::get_daily_limit($user_info->id);
        if($check_limit !=NULL)
        {
            if (ServiceHelper::limit_check($user_info->id, $euro_amount)) {
                $r_bal = (\app\Library\AppHelper::get_remaning_limit_balance(auth()->user()->id));
                $daily_limit = (\app\Library\AppHelper::get_daily_limit(auth()->user()->id));
                $getBalance = (\app\Library\AppHelper::getBalance(auth()->user()->id,auth()->user()->currency, false));
                $blink_limit = str_replace('-', '', $r_bal);
                $manager_id =(auth()->user()->parent_id);
                if($manager_id != '')
                {
                    $result = \app\User::where('id', $manager_id)->orderBy('id', 'DESC')->first();
                    $emails = [$result->email,'balaji@prepaysolution.in'];
                }
                else
                {
                    $result = \app\User::where('id', 1)->orderBy('id', 'DESC')->first();
                    $emails = [$result->email];
                }
                $send_email_data = array(
                    'retailer_name' => auth()->user()->username,
                    'manager_name' => $result->username,
                    'current_bal' => $getBalance,
                    'total_limit' => $daily_limit,
                    'current_limit' => $blink_limit,
                );
                \Mail::send('emails.daily_limit_alert', $send_email_data, function ($message) use ($emails) {
                    $message->from('noreply@tamaexpress.com', 'Tama Retailer');
                    $message->to($emails)->subject('Tama Daily Limit Alert');
                });
                AppHelper::logger('warning', 'Daily Limit Exceed', $user_info->username . 'Daily limit exceed to confirm tama topup order', $request->all());
                Log::warning('TamaTopup Daily Limit Exceed => ' . $user_info->username . ' => ' . $user_info->id);
                return redirect('tama-topup')
                    ->with('message', trans('common.parent_rule_failed'))
                    ->with('message_type', 'warning');
            }
        }
        if (ServiceHelper::parent_rule_check($user_info->parent_id, $euro_amount,$this->service_id)) {
            //parent does not have enough money or credit limit
            //order will be failed
            AppHelper::logger('warning', 'Parent Rule Failed', $user_info->username . ' parent does not have enough balance or credit limit to confirm tama topup order', $request->all());
            Log::warning('TamaFamily Parent Rule Failed => ' . $user_info->username . ' => ' . $user_info->parent_id);
            return redirect()->back()
                ->with('message', trans('common.parent_rule_failed'))
                ->with('message_type', 'warning');
        }
        $current_balance = AppHelper::getBalance($user_info->id, $user_info->currency, false);
        $user_service_commission = ServiceHelper::get_service_commission($user_info->id, $this->service_id);//service_id may change
        $order_amount = ServiceHelper::calculate_commission($euro_amount, $user_service_commission);
        $user_credit_limit = AppHelper::get_credit_limit($user_info->id);
        $sale_margin = ServiceHelper::calculate_sale_margin($euro_amount, $order_amount);
        if ($current_balance < $order_amount) {
            //check with credit limit
            if (ServiceHelper::check_with_credit_limit($order_amount, $current_balance, $user_credit_limit) == false) {
                AppHelper::logger('warning', 'TamaFamily Balance Error', $user_info->username . ' does not have enough balance or credit limit to confirm tamafamily order', $request->all());
                return redirect()->back()
                    ->with('message', trans('common.msg_order_failed_due_bal'))
                    ->with('message_type', 'warning');
            }
        }
        $responseData =[];
        $transID = "TF".date("y") . strtoupper(date('M')) . date('d') . date('His');
        try{
            $response = $this->client->request('POST', 'mytama/topup',[
                'headers'  => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer ".API_TOKEN
                ],
                'form_params' => [
                    'mobile_no' => $mobile_number,
                    'amount' => $request->amount,
                ]
            ]);
            $response_json = json_decode((string)$response->getBody(),true);
            $responseData = $response_json['data'];
            $track_order_id = TrackOrder::insertGetId([
                'trans_id' => $transID,
                'user_id' => $user_info->id,
                'api_order_id' => $responseData['order_id'],
                'api_trans_id' => $responseData['transaction_id'],
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => auth()->user()->id,
                'remarks' => "Order Transaction is about to initiate..."
            ]);
        }catch (BadResponseException $e){
            $response = $e->getResponse();
            $responseBodyAsString =  json_decode((string)$response->getBody()->getContents(),true);
            AppHelper::logger('warning',"TamaFamily API",'TamaFamily API HTTP Status '.$e->getCode(),$responseBodyAsString,true);
            return redirect()->back()->with('message',trans('common.msg_order_failed'))
                ->with('message_type','warning');
        }
        try {
            \DB::beginTransaction();
            $after_order_balance = number_format((float)$current_balance - $order_amount, 2, '.', '');
            $order_desc = $order_comment;
            $txn_ref = TRANSACTION_PREFIX . ServiceHelper::genTransID(5);
            $created_at = date("Y-m-d H:i:s");
            //make transaction
            $trans_id = ServiceHelper::sync_transaction($user_info->id, $created_at,'debit', $order_amount, $current_balance, $after_order_balance, $order_desc);
            //make order
            $order_id = Order::insertGetId([
                'date' => $created_at,
                'user_id' => $user_info->id,
                'service_id' => $this->service_id,
                'order_status_id' => 7,
                'transaction_id' => $trans_id,
                'txn_ref' => $txn_ref,
                'comment' => $order_desc,
                'currency' => $user_info->currency,
                'public_price' => $euro_amount,
                'sale_margin' => $sale_margin,
                'buying_price' => $order_amount,
                'order_amount' => $order_amount,
                'grand_total' => $order_amount,
                'created_at' => $created_at,
                'created_by' => $user_info->id,
            ]);
            //getting the user old balance
            $old_mytama_balance = str_replace("€",'',$responseData['old_balance']);
            $new_mytama_balance = str_replace("€",'',$responseData['new_balance']);
            //insert order items
            $order_item_id = OrderItem::insertGetId([
                'order_id' => $order_id,
                'app_mobile' => $mobile_number,
                'app_old_balance' => $old_mytama_balance,
                'app_new_balance' => $new_mytama_balance,
                'created_at' => $created_at,
                'created_by' => $user_info->id,
            ]);
            //update the order item id to order
            Order::where('id',$order_id)->update([
                'order_item_id' => $order_item_id
            ]);
            $parent_user = User::find($user_info->parent_id);
            if(!empty($user_info->parent_id) && $parent_user && $parent_user->group_id != 2){
                $parent_user_commission = ServiceHelper::get_service_commission($parent_user->id,$this->service_id);
                $parent_current_balance = AppHelper::getBalance($parent_user->id,$parent_user->currency,false);
                $parent_actual_commission = $parent_user_commission - $user_service_commission;
                $buying_price_parent = ServiceHelper::calculate_commission($euro_amount,$parent_user_commission);
                $order_amount_parent = ServiceHelper::calculate_commission($euro_amount,$parent_actual_commission);
                $parent_sale_margin = ServiceHelper::calculate_sale_margin($order_amount,$buying_price_parent);
                $parent_after_order_balance = number_format((float)$parent_current_balance - $buying_price_parent, 2, '.', '');
                //make transaction for parent
                $parent_trans_id = ServiceHelper::sync_transaction($parent_user->id,$created_at,'debit',$buying_price_parent,$parent_current_balance,$parent_after_order_balance,$order_desc);
                //parent order insertion
                $parent_order_id = Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $user_info->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 7,
                    'transaction_id' => $parent_trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $euro_amount,
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
                $app_commission = optional(AppCommission::where('service_id',$this->service_id)->first())->commission;
                $app_actual_commission = $app_commission - $parent_user_commission;
                $buying_price_app = ServiceHelper::calculate_commission($euro_amount,$app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($euro_amount,$app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($buying_price_parent,$buying_price_app);
                Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $parent_user->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 7,
                    'transaction_id' => $trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $euro_amount,
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
            }else{
                //use the app commission to update order buying_price
                $app_commission = optional(AppCommission::where('service_id',$this->service_id)->first())->commission;
                $app_actual_commission = $app_commission - $user_service_commission;
                $buying_price_app = ServiceHelper::calculate_commission($euro_amount,$app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($euro_amount,$app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($euro_amount,$order_amount_app);
                Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $user_info->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 7,
                    'transaction_id' => $trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $euro_amount,
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
            \DB::commit();
            AppHelper::logger('success','TamaFamily Order #'.$order_id,$order_desc);
            $url = auth()->user()->group_id == 4 ? "orders" : "my/orders";
            return redirect($url)->with('message',trans('service.tama_order_placed_suc_callback'))->with('message_type','success');
        }catch (\Exception $e){
            \DB::rollback();
            $exception_id = 'TFEX' . AppHelper::Numeric(5);//to know more about exception
            $exceptions = [
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Code' => $e->getCode()
            ];
            Log::emergency(auth()->user()->username . " TamaFamily API Exception => " . $e->getMessage());
            AppHelper::logger('warning', 'TamaFamily Exception ' . $exception_id, $e->getMessage(),$exceptions);
            return redirect()->back()
                ->with('message', trans('common.error_confirm_order') . ' ' . $exception_id)
                ->with('message_type', 'warning');
        }
    }
}
