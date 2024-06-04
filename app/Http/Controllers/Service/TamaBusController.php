<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\TamaBus\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use app\Library\AppHelper;
use app\Library\SecurityHelper;
use app\Library\ServiceHelper;
use App\User;
use App\DingDenomination;
use App\Models\AppCommission;
use App\Models\Order;
use GuzzleHttp\Client;

class TamaBusController extends Controller
{
    private $service_id;
    private $decipher;
    private $client;
    function __construct()
    {
        parent::__construct();
        $this->service_id = 9;
        $this->decipher = new SecurityHelper();
        $this->middleware(function ($request, $next) {
            if(API_TOKEN == '' || API_END_POINT == ''){
                AppHelper::logger('warning','API SETTINGS ERROR',"Missing API Token or API end point url",request()->all(),true);
                return redirect()->back()
                    ->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            if(AppHelper::user_access($this->service_id,auth()->user()->id) == 0){
                AppHelper::logger('warning','Access Violation',auth()->user()->username. " trying to access Flixbus service",request()->all(),true);
                return redirect()->back()
                    ->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            //lets check with this user parent has access
            if(\app\Library\AppHelper::skip_service_as_menu('flix-bus') == false){
                AppHelper::logger('warning', 'Access Violation', auth()->user()->username . " trying to access Flixbus service but parent of this user does not have a access", request()->all());
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
//    fetch cites and stations
    function index()
    {
        try {
            $response = $this->client->request('GET', 'flix-bus', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $citiesAndStations = json_decode((string)$response->getBody(), true);
            }
            return view('service.tama-bus.index', [
                'page_title' => "Flix Bus",
                'cities' => $citiesAndStations['data']
            ]);

        }catch (\Exception $e){
            AppHelper::logger('warning',"Flix bus API HTTP Exception",$e->getMessage(),$e,true);
            return redirect('dashboard')
                ->with('message', trans('common.access_violation'))
                ->with('message_type', 'warning');
        }
    }
    //search buses and details
    function search(Request $request, $operation)
    {

        $result = [];
        if($request->cityFrom == "" || $request->from == "" || $request->cityTo == "" ||$request->to == "" ||$request->passengers == "" || $request->departure_date == "")
        {
            Log::warning("Search Flix Bus Validation Failed");
            return responder()->error("BAD_REQUEST", "Missing Field")->respond(400);
        }
        $params = [
            "search_by" => 'cities',
            "from"=>$request->cityFrom,
            "to"=>$request->cityTo,
            "departure_date"=>$request->departure_date,
            "d_date"=>$request->departure_date,
            "adult"=> ($request->adult) ? $request->adult : '0',
            "children"=>($request->children) ? $request->children : '0',
            "bikes"=>($request->bikes) ? $request->bikes : '0',
            "currency"=>'EUR',
            "cityFrom"=> $request->from,
            "cityTo"=> $request->to,
            "passengers"=> $request->passengers,
            "sort_by" => $request->sort_by
        ];
        try {
            $response = $this->client->request('GET', 'flix-bus/search?'. http_build_query($params), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $results = json_decode((string)$response->getBody(), true);
                return [
                    'status' => $response->getStatusCode(),
                    'message' => "Buses Details fetched",
                    'data' => $results
                ];
            }
        } catch (\Exception $e) {
            AppHelper::logger('warning',"Flix bus API HTTP Exception",$e->getMessage(),$e,true);
            return responder()->error("BAD_REQUEST", "Missing Field")->respond(400);
        }
    }

    //create reservations
    function create_reservations(Request $request)
    {
        $result = [];
        if($request->trip_uid == "" || $request->currency == "" )
        {
            Log::warning("Flix Bus Validation Failed");
            return responder()->error("BAD_REQUEST", "Missing Field")->respond(400);
        }
        $params["trip_uid"] =$request->trip_uid;
        $params["adult"] = ($request->adult) ? $request->adult : '0';
        $params["children"] =($request->children) ? $request->children : '0';
        $params["bikes"] = ($request->bikes) ? $request->bikes : '0';
        $params["currency"] ='EUR';
        try {

            $response = $this->client->request('POST', 'flix-bus/create_reservations', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ],
                'form_params' => $params
            ]);

            if ($response->getStatusCode() == 200) {
                $results = json_decode((string)$response->getBody(), true);
                return [
                    'status' => $response->getStatusCode(),
                    'message' => "Buses Details fetched successfully",
                    'data' => $results
                ];
            }
        } catch (\Exception $e) {
            return responder()->error("EXCEPTION", "Please try again later!")->respond(500);
        }
    }


    //order succesfully
    function add_passenger_details(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "firstname.*" => "required",
            "lastname.*" => "required",
            "birthdate.*" => "required",
            "reservation_token.*" => "required",
            "reservation_id.*" => "required",
            "email" => "required",
        ]);

        if($validator->fails())
        {
            Log::warning("FlixBus validation failed",[$request->except('_token')]);
            AppHelper::logger('warning', 'FlixBus API', "Validation Error Missing Field" ,$request->except('_token'));
            return responder()->error(201, 'Please fill neccessary fields, validation failed!');
        }
        $params["reservation_token"] =$request->reservation_token[0];
        $params["reservation_id"] =$request->reservation_id[0];
        $params["email"] =$request->email;
        $params["total_price"] =$request->total_price;

        for($i=0; $i < count($request->type); $i++ ){
            $params["passengers[$i][type]"] = $request->type[$i];
            $params["passengers[$i][product_type]"] = $request->product_type[$i];
            $params["passengers[$i][reference_id]"] = $request->reference_id[$i];
            $params["passengers[$i][passenger_no]"] = $request->passenger_no[$i];
            $params["passengers[$i][firstname]"] = $request->firstname[$i];
            $params["passengers[$i][lastname]"] = $request->lastname[$i];
            $params["passengers[$i][birthdate]"] = $request->birthdate[$i];
        }
        $user_info = User::find(auth()->user()->id);
        $check_limit = AppHelper::get_daily_limit(auth()->user()->id,auth()->user()->currency,true);
        $euro_amount = str_replace(',', '', $request->input('total_price'));
        $user_service_commission = ServiceHelper::get_service_commission($user_info->id, $this->service_id);//service_id may change
        $current_balance = AppHelper::getBalance($user_info->id, $user_info->currency, false);
        $order_amount = ServiceHelper::calculate_commission($euro_amount, $user_service_commission);
        $user_credit_limit = AppHelper::get_credit_limit($user_info->id);
        $sale_margin = ServiceHelper::calculate_sale_margin($euro_amount, $order_amount);
        $after_order_balance = number_format((float)$current_balance - $order_amount, 2, '.', '');
        $created_at = date("Y-m-d H:i:s");
        $mobile_operator ="flixbus";
        $mobile_number ="917904721979";
        if($request->phone_number){
            $mobile_number = $request->phone_number;
        }
        if($check_limit !=NULL)
        {
            if (ServiceHelper::limit_check($user_info->id, $euro_amount)) {
                //Daily Limit for this user
                //order will be failed
                $r_bal = (\app\Library\AppHelper::get_remaning_limit_balance(auth()->user()->id));
                $daily_limit = (\app\Library\AppHelper::get_daily_limit(auth()->user()->id));
                $getBalance = (\app\Library\AppHelper::getBalance(auth()->user()->id,auth()->user()->currency, false));
                $blink_limit = str_replace('-', '', $r_bal);
                $manager_id =(auth()->user()->parent_id);
                $getBalance = (\app\Library\AppHelper::sendMail($r_bal,$daily_limit,$getBalance,$blink_limit,$manager_id,auth()->user()->username));
                AppHelper::logger('warning', 'Daily Limit Exceed', $user_info->username . 'Daily limit exceed to confirm Flix bus order', $request->all());
                Log::warning('Flix Bus Daily Limit Exceed => ' . $user_info->username . ' => ' . $user_info->id);
                return responder()->error(400, trans('common.contact_manager'));
            }
        }
        if (ServiceHelper::parent_rule_check($user_info->parent_id, $euro_amount,$this->service_id)) {
            //parent does not have enough money or credit limit
            //order will be failed
            AppHelper::logger('warning', 'Parent Rule Failed', $user_info->username . ' parent does not have enough balance or credit limit to confirm Flix bus order', $request->all());
            Log::warning('Flix Bus Parent Rule Failed => ' . $user_info->username . ' => ' . $user_info->parent_id);
            return responder()->error(400, trans('common.parent_rule_failed'));
        }

        if ($current_balance < $order_amount) {
            //check with credit limit
            if (ServiceHelper::check_with_credit_limit($order_amount, $current_balance, $user_credit_limit) == false) {
                AppHelper::logger('warning', 'Flix Bus Balance Error', $user_info->username . ' does not have enough balance or credit limit to confirm Flix Bus order', $request->all());
                return responder()->error(400, trans('common.msg_order_failed_due_bal'));
            }
        }

        $transID = "TT".date("y") . strtoupper(date('M')) . date('d') . date('His').Rand(111,999);
        try {
            \DB::beginTransaction();
            $tt_txn_id = TRANSACTION_PREFIX . ServiceHelper::genTransID(5);
            $response = $this->client->request('POST', 'flix-bus/add_passengers_details', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ],
                'form_params' => $params
            ]);

            //response from Flix demat
            if($response->getStatusCode() == 200){
                //Data From Api response
                $print_ticket1 = json_decode((string)$response->getBody(), true);
                Log::info("FlixBus", [$print_ticket1]);
                $txn_ref = isset($print_ticket1['txn_ref']) ? $print_ticket1['txn_ref'] : $tt_txn_id;
                $print_ticket = 'flix-bus/download/'.$print_ticket1['print_ticket'][0].','.$print_ticket1['print_ticket'][1];
                $order_comment = $user_info->username . " Flix bus for " . $euro_amount . " Print Ticket Link is " . $print_ticket1['print_ticket'][0];
                $order_desc = $order_comment;
                $ins = $print_ticket1['print_ticket'][0];
                $link = $print_ticket1['print_ticket'][1];
            }

            //make transaction
            $trans_id = ServiceHelper::sync_transaction($user_info->id, $created_at, 'debit', $order_amount, $current_balance, $after_order_balance, $order_desc);
            //Insert order
            $order_id = ServiceHelper::save_order('',$created_at, $user_info->id, $this->service_id, '7', $trans_id, $txn_ref, $order_desc,$user_info->currency,$euro_amount,$sale_margin,$order_amount,$order_amount,$order_amount,NULL,0,0);


            //insert order items
            $order_item_id = ServiceHelper::save_orders_items($order_id, $mobile_number, $euro_amount, $mobile_operator, $ins, $link, $created_at,$user_info->id);

            //update the order item id to order
            Order::where('id',$order_id)->update([
                'order_item_id' => $order_item_id
            ]);
            $parent_user = User::find($user_info->parent_id);
            if (!empty($user_info->parent_id) && $parent_user && $parent_user->group_id != 2) {

                $parent_user_commission = ServiceHelper::get_service_commission($parent_user->id, $this->service_id);
                $parent_current_balance = AppHelper::getBalance($parent_user->id, $parent_user->currency, false);
                $parent_actual_commission = $parent_user_commission - $user_service_commission;
                $buying_price_parent = ServiceHelper::calculate_commission($euro_amount, $parent_user_commission);

                $order_amount_parent = ServiceHelper::calculate_commission($euro_amount, $parent_actual_commission);
                $parent_sale_margin = ServiceHelper::calculate_sale_margin($order_amount, $buying_price_parent);
                $parent_after_order_balance = number_format((float)$parent_current_balance - $buying_price_parent, 2, '.', '');

                //make transaction for parent
                $parent_trans_id = ServiceHelper::sync_transaction($parent_user->id, $created_at, 'debit', $buying_price_parent, $parent_current_balance, $parent_after_order_balance, $order_desc);

                //parent order insertion
                $parent_order_id = ServiceHelper::save_order($order_desc,$created_at, $user_info->id, $this->service_id, '7', $parent_trans_id, $txn_ref, $order_desc,$user_info->currency,$euro_amount,$parent_sale_margin,$order_amount,$buying_price_parent,$order_amount,$order_item_id,1,0);


                $app_commission = optional(AppCommission::where('service_id', $this->service_id)->first())->commission;
                $app_actual_commission = $app_commission - $parent_user_commission;
                $buying_price_app = ServiceHelper::calculate_commission($euro_amount, $app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($euro_amount, $app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($buying_price_parent, $buying_price_app);
                Log::info("commissions", [
                    'app commission '=> $app_commission,
                    'user service commission' => $user_service_commission,
                    'buying_price_app' => $buying_price_app,
                    'order_amount_app' => $order_amount_app,
                    'app_sale_margin' => $app_sale_margin,
                ]);
                ServiceHelper::save_order($order_desc,$created_at, $parent_user->id, $this->service_id, '7', $trans_id, $txn_ref, $order_desc,$user_info->currency,$euro_amount,$app_sale_margin,$buying_price_parent,$buying_price_app,$buying_price_parent,$order_item_id,1,0);
            } else {
                //use the app commission to update order buying_price
                $app_commission = optional(AppCommission::where('service_id', $this->service_id)->first())->commission;
                $app_actual_commission = $app_commission - $user_service_commission;
                $buying_price_app = ServiceHelper::calculate_commission($euro_amount, $app_commission);
                $order_amount_app = ServiceHelper::calculate_commission($euro_amount, $app_actual_commission);
                $app_sale_margin = ServiceHelper::calculate_sale_margin($euro_amount, $order_amount_app);
                Log::info("commissions", [
                    'app commission '=> $app_commission,
                    'user service commission' => $user_service_commission,
                    'buying_price_app' => $buying_price_app,
                    'order_amount_app' => $order_amount_app,
                    'app_sale_margin' => $app_sale_margin,
                ]);
                ServiceHelper::save_order($order_desc,$created_at, $parent_user->id, $this->service_id, '7', $trans_id, $txn_ref, '',$user_info->currency,$euro_amount,$app_sale_margin, $order_amount,$buying_price_app, $order_amount,$order_item_id,1,0);

            }
            \DB::commit();
            AppHelper::logger('success', 'Flix Bus Order #' . $order_id, $order_desc);
            return $print_ticket;
        }
        catch (\Exception $e) {
            \DB::rollback();
            $exception_id = 'TTEX' . AppHelper::Numeric(5);//to know more about exception
            $exceptions = [
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Code' => $e->getCode()
            ];
            Log::emergency(auth()->user()->username . " Flix Bus API Exception => " . $e->getMessage());
            AppHelper::logger('warning', 'Flix Bus Exception ' . $exception_id, $e->getMessage(),$exceptions);
            return responder()->error(500, 'Flix Bus Exception ');
        }
    }
    function download($link)
    {
        $params = explode (",", $link);
        $response = $this->client->request('GET', 'flix-bus/download/'.$link, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer " . API_TOKEN
            ],
        ]);
        $res = json_decode((string)$response->getBody(), true);
        return redirect()->to($res['reminder_link']);
    }
}
