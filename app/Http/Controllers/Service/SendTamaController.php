<?php

namespace App\Http\Controllers\Service;

use App\Events\SendTamaOrder;
use app\Library\ApiHelper;
use app\Library\AppHelper;
use app\Library\SecurityHelper;
use app\Library\ServiceHelper;
use App\Models\AppCommission;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\ServiceConfig;
use App\Models\TrackOrder;
use App\User;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use Validator;
use GuzzleHttp\Client;

class SendTamaController extends Controller
{
    private $service_id;
    private $decipher;
    private $client;

    function __construct()
    {
        parent::__construct();
        $this->service_id = 1;
        $this->decipher = new SecurityHelper();
        $this->middleware(function ($request, $next) {
            if (API_TOKEN == '' || API_END_POINT == '') {
                AppHelper::logger('warning', 'API SETTINGS ERROR', "Missing API Token or API end point url", request()->all(), true);
                return redirect()->back()
                    ->with('message', trans('common.access_violation'))
                    ->with('message_type', 'warning');
            }
            if (AppHelper::user_access($this->service_id, auth()->user()->id) == 0) {
                AppHelper::logger('warning', 'Access Violation', auth()->user()->username . " trying to access sendtama service", request()->all());
                return redirect()->back()
                    ->with('message', trans('common.access_violation'))
                    ->with('message_type', 'warning');
            }
            //lets check with this user parent has access
            if(\app\Library\AppHelper::skip_service_as_menu('send-tama') == false){
                AppHelper::logger('warning', 'Access Violation', auth()->user()->username . " trying to access sendtama service but parent of this user does not have a access", request()->all());
                return redirect('dashboard')
                    ->with('message', trans('common.access_violation'))
                    ->with('message_type', 'warning');
            }
            $this->client = new Client([
                'base_uri' => API_END_POINT,
                'timeout' => 120,
            ]);
            return $next($request);
        });
    }

    /**
     * View all send tama countries
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        try{
            $response = $this->client->request('GET', 'send-tama', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode((string)$response->getBody(), true);
                $this->data['countries'] = $data['data'];
                $this->data['page_title'] = "Send Tama";
                return view('service.send-tama.index', $this->data);
            } else {
                AppHelper::logger('warning', "Send Tama API", 'Send Tama API HTTP Status ' . $response->getStatusCode(), $response, true);
                return redirect()->back()->with('message', trans('service.service_unavailable'))
                    ->with('message_type', 'warning');
            }
        }catch (\Exception $e){
            AppHelper::logger('warning', "Send Tama API HTTP Exception", $e->getMessage(),'', true);
            return redirect()->back()->with('message', trans('service.service_unavailable'))
                ->with('message_type', 'warning');
        }
    }

    /**
     * View all products
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function view_products(Request $request, $id)
    {
        $dec_id = $this->decipher->decrypt($id);
        try{
            $response = $this->client->request('GET', 'send-tama/' . $dec_id . '/products', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $result = json_decode((string)$response->getBody(), true);
                $this->data = [
                    'products' => $result['data'],
                    'page_title' => ucfirst(trans('view')) . " " . optional(Country::find($dec_id))->nice_name . " " . trans('common.lbl_product'),
                    'country_name' => optional(Country::find($dec_id))->nice_name
                ];
                return view('service.send-tama.products', $this->data);
            } else {
                AppHelper::logger('warning', "Send Tama API", 'Send Tama API HTTP Status ' . $response->getStatusCode(), $response, true);
                return redirect()->back()->with('message', trans('service.service_unavailable'))
                    ->with('message_type', 'warning');
            }
        }catch (\Exception $e){
            AppHelper::logger('warning', "Send Tama API HTTP Exception", $e->getMessage(), '', true);
            return redirect()->back()->with('message', trans('service.service_unavailable'))
                ->with('message_type', 'warning');
        }
    }

    /**
     * View product detail
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function view_product($id)
    {
        $dec_id = $this->decipher->decrypt($id);
        try{
            $response = $this->client->request('GET', 'send-tama/product/' . $dec_id, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $result = json_decode((string)$response->getBody(), true);
                $this->data = [
                    'product' => $result['data'],
                    'page_title' => ucfirst(trans('view')) . " " . $result['data']['product_name'],
                    'country' => Country::where('nice_name', 'like', "%" . $result['data']['country'] . "%")->first()
                ];
                return view('service.send-tama.view', $this->data);
            } else {
                AppHelper::logger('warning', "Send Tama API", 'Send Tama API HTTP Status ' . $response->getStatusCode(), $response, true);
                return redirect()->back()->with('message', trans('service.service_unavailable'))
                    ->with('message_type', 'warning');
            }
        }catch (\Exception $e){
            AppHelper::logger('warning', "Send Tama API HTTP Exception", $e->getMessage(), '', true);
            return redirect()->back()->with('message', trans('service.service_unavailable'))
                ->with('message_type', 'warning');
        }
    }

    /**
     * Confirm Send Tama Order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function confirm_order(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'sender_first_name' => "required",
            'sender_mobile' => 'required',
//            'sender_email' => 'required',
            'receiver_first_name' => 'required',
            'receiver_mobile' => 'required',
//            'receiver_email' => 'required',
        ]);
        if ($validator->fails()) {
            AppHelper::logger('warning', 'Confirm SendTama Validation Failed', 'Unable to place send tama order', $request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message', $html)
                ->with('message_type', 'warning');
        }
        //fetch the product info
        $user_info = User::find(auth()->user()->id);
        $dec_product_id = $request->product_id;
        $response = $this->client->request('GET', 'send-tama/product/' . $request->product_id, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer " . API_TOKEN
            ]
        ]);
        if ($response->getStatusCode() == 200) {
            $result = json_decode((string)$response->getBody(), true);
            $product = $result['data'];
        } else {
            AppHelper::logger('warning', "Send Tama API", 'Send Tama API HTTP Status ' . $response->getStatusCode(), $response, true);
            return redirect()->back()->with('message', trans('service.service_unavailable'))
                ->with('message_type', 'warning');
        }
        //lets check the parent balance or credit limit with in the order amount
        if (ServiceHelper::parent_rule_check(auth()->user()->parent_id, $product['product_cost'],$this->service_id)) {
            //parent does not have enough money or credit limit
            //order will be failed
            AppHelper::logger('warning', 'Parent Rule Failed', auth()->user()->username . ' parent does not have enough balance or credit limit to confirm send tama order', $request->all());
            Log::warning('Send Tama Parent Rule Failed => ' . auth()->user()->username . ' => ' . auth()->user()->parent_id);
            return redirect()->back()
                ->with('message', trans('common.parent_rule_failed'))
                ->with('message_type', 'warning');
        }
//        dd(ServiceHelper::parent_rule_check(auth()->user()->parent_id, $product['product_cost']));
        //check with user balance and credit limit
        $current_balance = AppHelper::getBalance($user_info->id, $user_info->currency, false);
        $user_service_commission = ServiceHelper::get_service_commission($user_info->id, 1);//service_id may change
        $order_amount = ServiceHelper::calculate_commission($product['product_cost'], $user_service_commission);
        $user_credit_limit = AppHelper::get_credit_limit($user_info->id);
        $sale_margin = ServiceHelper::calculate_sale_margin($product['product_cost'], $order_amount);
        if ($current_balance < $order_amount) {
            //check with credit limit
            if (ServiceHelper::check_with_credit_limit($order_amount, $current_balance, $user_credit_limit) == false) {
                AppHelper::logger('warning', 'Send Tama Balance Error', auth()->user()->username . ' does not have enough balance or credit limit to confirm send tama order', $request->all());
                return redirect()->back()
                    ->with('message', trans('common.msg_order_failed_due_bal'))
                    ->with('message_type', 'warning');
            }
        }
        $transID = "ST".date("y") . strtoupper(date('M')) . date('d') . date('His');
        try{
            $response = $this->client->request('POST', 'send-tama/confirm', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ],
                'form_params' => [
                    'product_id' => $request->product_id,
                    'sender_name' => $request->sender_first_name,
                    'sender_mobile' => $request->sender_mobile,
                    'receiver_name' => $request->receiver_first_name,
                    'receiver_mobile' => $request->receiver_mobile,
                    'sender_surname' => $request->sender_last_name,
                    'receiver_surname' => $request->receiver_last_name,
                    'order_comment' => $request->order_comment,
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
                    'api_trans_id' => $response_data['transaction_id'],
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth()->user()->id,
                    'remarks' => "Order Transaction is about to initiate..."
                ]);
            }else{
                AppHelper::logger('warning',"TamaApp API",'TamaApp API HTTP Status '.$response->getStatusCode(),$response,true);
                return redirect()->back()->with('message',trans('common.msg_order_failed'))
                    ->with('message_type','warning');
            }
        }catch (BadResponseException $e){
            $response = $e->getResponse();
            $responseBodyAsString =  json_decode((string)$response->getBody()->getContents(),true);
            AppHelper::logger('warning',"SendTama API",'SendTama API HTTP Status '.$e->getCode(),$responseBodyAsString,true);
            return redirect()->back()->with('message',trans('common.msg_order_failed'))
                ->with('message_type','warning');
        }
        try {
            \DB::beginTransaction();
            $after_order_balance = number_format((float)$current_balance - $order_amount, 2, '.', '');
            $backup_comment = $user_info->username . ' make an order of ' . $product['product_name'] . ' with the amount of ' . $product['product_cost'] . '(' . $order_amount . ')';
            $order_desc = !empty($request->order_comment) ? $request->order_comment : $backup_comment;
            $txn_ref = TRANSACTION_PREFIX . ServiceHelper::genTransID(5);
            $created_at = date("Y-m-d H:i:s");
            //make transaction
            $trans_id = ServiceHelper::sync_transaction($user_info->id, $created_at, 'debit', $order_amount, $current_balance, $after_order_balance, $order_desc);
            //make order
            $order_id = Order::insertGetId([
                'date' => $created_at,
                'user_id' => $user_info->id,
                'service_id' => $this->service_id,
                'order_status_id' => 1, //initially 1 as Order Accepted
                'transaction_id' => $trans_id,
                'txn_ref' => $txn_ref,
                'comment' => $order_desc,
                'currency' => $user_info->currency,
                'public_price' => $product['product_cost'],
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
                'product_id' => $dec_product_id,
                'sender_first_name' => $request->sender_first_name,
                'sender_last_name' => $request->sender_last_name,
                'sender_mobile' => $request->sender_mobile,
                'sender_email' => $request->sender_email,
                'receiver_first_name' => $request->receiver_first_name,
                'receiver_last_name' => $request->receiver_last_name,
                'receiver_mobile' => $request->receiver_mobile,
                'receiver_email' => $request->receiver_email,
                'tama_pin' => ServiceHelper::genRandomPin(9),
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
                $buying_price_parent = ServiceHelper::calculate_commission($product['product_cost'], $parent_user_commission);
                $order_amount_parent = ServiceHelper::calculate_commission($product['product_cost'], $parent_actual_commission);
                $parent_sale_margin = ServiceHelper::calculate_sale_margin($order_amount, $buying_price_parent);
                $parent_after_order_balance = number_format((float)$parent_current_balance - $buying_price_parent, 2, '.', '');
                //make transaction for parent
                $parent_trans_id = ServiceHelper::sync_transaction($parent_user->id, $created_at, 'debit', $buying_price_parent, $parent_current_balance, $parent_after_order_balance, $order_desc);
                //parent order insertion
                $parent_order_id = Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $user_info->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 1, //initially 1 as Order Accepted
                    'transaction_id' => $parent_trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $product['product_cost'],
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
                $buying_price_app = ServiceHelper::calculate_commission($product['product_cost'], $app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($product['product_cost'], $app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($buying_price_parent, $buying_price_app);
                Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $parent_user->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 1, //initially 1 as Order Accepted
                    'transaction_id' => $trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $product['product_cost'],
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
                $buying_price_app = ServiceHelper::calculate_commission($product['product_cost'], $app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($product['product_cost'], $app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($product['product_cost'], $order_amount_app);
                Order::insertGetId([
                    'date' => $created_at,
                    'user_id' => $user_info->id,
                    'service_id' => $this->service_id,
                    'order_status_id' => 1, //initially 1 as Order Accepted
                    'transaction_id' => $trans_id,
                    'txn_ref' => $txn_ref,
                    'comment' => $order_desc,
                    'currency' => $user_info->currency,
                    'public_price' => $product['product_cost'],
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
                'remarks' => "Order placed successfully!",
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id
            ]);
            \DB::commit();
            AppHelper::logger('success', 'Send Tama Order #' . $order_id, $order_desc);
            $url = auth()->user()->group_id == 4 ? "orders" : "my/orders";
            return redirect($url)->with('message', trans('service.tama_order_placed_suc_callback'))->with('message_type', 'success');
        }
        catch (\Exception $e) {
            \DB::rollback();
            $exception_id = 'STEX' . AppHelper::Numeric(5);//to know more about exception
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
            Log::emergency(auth()->user()->username . " Send Tama API Exception => " . $e->getMessage());
            AppHelper::logger('warning', 'Send Tama Exception ' . $exception_id, $e->getMessage(), $exceptions);
            return redirect()->back()
                ->with('message', trans('common.error_confirm_order') . ' ' . $exception_id)
                ->with('message_type', 'warning');
        }
    }
}
