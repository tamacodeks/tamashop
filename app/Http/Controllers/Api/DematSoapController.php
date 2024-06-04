<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Aleda\BaseSoapController;
use App\Http\Controllers\Aleda\InstanceSoapClient;
use app\Library\ApiHelper;
use App\Models\AledaServiceRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class DematSoapController extends BaseSoapController
{
    private $service;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get prepaidBalance from ALEDA Service
     * @return object
     */
    function getIncurBalance()
    {
        $trans_id = ApiHelper::generateTransID();
        $checkOccurrence = AledaServiceRequest::where('method','consultIncur')->orderBy('id','DESC')->first();
        if(collect($checkOccurrence)->count() > 0){
            $last_time_type =  date("s",strtotime($checkOccurrence->invoke_at));
        }else{
            $last_time_type = "start";
        }
        $params = [
            'configId' => config('aleda.config_id'),
            'login' => config('aleda.login'),
            'pwd' => config('aleda.password'),
            'terminalChecker' => config('aleda.terminal'),
            'transId' => str_replace("_".auth()->user()->id, "", $trans_id),
            'lastTimeType' => $last_time_type,
            'ciDataHolder' => [
                'incur' => ""
            ]
        ];
        $service_req_id = AledaServiceRequest::insertGetId([
            'method' => "consultIncur",
            'payload' => json_encode($params),
            'trans_id' => $trans_id,
            'invoke_at' => date("Y-m-d H:i:s"),
            'invoke_by' => auth()->check() ? auth()->user()->id : "",
            'status' => 0
        ]);
        try {
            self::setWsdl(config('aleda.web_service_url'));
            $this->service = InstanceSoapClient::init();
            $response = $this->service->consultIncur($params);
            AledaServiceRequest::where('id',$service_req_id)->update([
                   'status' => 1,
                   'http_code' => 200,
                   'response' => json_encode($response)
            ]);
            $balance = $response->ciDataHolder;
            $return = $response->return;
            if($return->resultCode == "0"){
                Log::info("aleda balance was ".$balance->incur,[$response]);
                return $balance->incur;
            }else{
                Log::info("aleda balance error response was ".$balance,[$response]);
                return (object)[
                    'error' => "0"
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Exception while fetching aleda balance ".$e->getMessage());
            AledaServiceRequest::where('id',$service_req_id)->update([
                'http_code' => $e->getCode(),
                'response' => $e->getMessage()
            ]);
            return (object)[
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Serial, Pin and Validity of single card
     * @param $product_code
     * @return object
     */
    function sellDematModeXS($product_code)
    {
        $trans_id = ApiHelper::generateTransID();
        $checkOccurrence = AledaServiceRequest::where('method','sellDematModeXS')->orderBy('id',"DESC")->first();
        if(collect($checkOccurrence)->count() > 0){
            $last_time_type =  date("s",strtotime($checkOccurrence->invoke_at));
            $last_xs = $checkOccurrence->trans_id;
        }else{
            $last_time_type = "start";
            $last_xs = "start";
        }
        $params = [
            'configId' => config('aleda.config_id'),
            'login' => config('aleda.login'),
            'pwd' => config('aleda.password'),
            'terminalChecker' => config('aleda.terminal'),
            'transId' =>  str_replace("_".auth()->user()->id, "", $trans_id),
            'lastTimeType' => $last_time_type,
            'subType' => "120", //fixed
            'productCode' => $product_code,
            'qty' => 1, //fixed
            'lastXS' => $last_xs,
            'qryMode' => 0,//fixed
            'sellDematXSDataHolder' => [
                'productASList' => [
                    'serialNb' => "",
                    'secretCode' => "",
                    'validityDate' => ""
                ],
                'pub' => "",
                'bonus' => "",
                'valueRegister' => ""
            ]
        ];
        $service_req_id = AledaServiceRequest::insertGetId([
            'method' => "sellDematModeXS",
            'payload' => json_encode($params),
            'trans_id' => $trans_id,
            'invoke_at' => date("Y-m-d H:i:s"),
            'invoke_by' => auth()->check() ? auth()->user()->id : "",
            'status' => 0
        ]);
        try {
            self::setWsdl(config('aleda.web_service_url'));
            $this->service = InstanceSoapClient::init();
            $response = $this->service->sellDematModeXS($params);
            AledaServiceRequest::where('id',$service_req_id)->update([
                'status' => 1,
                'http_code' => 200,
                'response' => json_encode($response)
            ]);
            $collection = collect($params);
            $filtered = $collection->except(['configId','login','pwd','terminalChecker']);
            Log::info("SellDematModeXS params data for User ".auth()->user()->id."_".auth()->user()->username,[$filtered->all()]);
            $product = $response->sellDematXSDataHolder;
            $error_check = $response->return;
            if($error_check->resultCode != 0){
                Log::warning("SellDematModeXS resultCode Exception => ".$error_check->errorText." ".$error_check->resultCode." product code was ".$product_code);
                throw new \Exception(trans('myservice.unable_to_print'));
            }
            Log::info("SellDematModeXS Success =>  product code was ".$product_code,[$product]);
            return $product;
        } catch (\Exception $e) {
            Log::warning("Exception while fetching card info ".$e->getMessage());
            AledaServiceRequest::where('id',$service_req_id)->update([
                'http_code' => $e->getCode(),
                'response' => $e->getMessage()
            ]);
            return (object)[
                'error' => $e->getMessage()
            ];
        }
    }



    function sellDematModeES($product_code)
    {
        $trans_id = ApiHelper::generateTransID();
        $checkOccurrence = AledaServiceRequest::where('method','sellDematModeES')->orderBy('id',"DESC")->first();
        if(collect($checkOccurrence)->count() > 0){
            $last_time_type =  date("s",strtotime($checkOccurrence->invoke_at));
            $last_es = $checkOccurrence->trans_id;
        }else{
            $last_time_type = "start";
            $last_es = "start";
        }
        $params = [
            'configId' => config('aleda.config_id'),
            'login' => config('aleda.login'),
            'pwd' => config('aleda.password'),
            'terminalChecker' => config('aleda.terminal'),
            'transId' =>  str_replace("_".auth()->user()->id, "", $trans_id),
            'lastTimeType' => $last_time_type,
            'subType' => "100", //get a confirmation by response later it will 120
            'productCode' => $product_code,
            'data' => "",
            'codeAction' => "00",
            'lastES' => $last_es,
            'qryMode' => 0,//fixed
            'sellDematESDataHolder' => [
                'operatorMsg' => "",
                'operatorTransId' => "",
                'data' => "",
                'redirect' => "",
                'nextSubType' => "",
                'nextMessage' => "",
                'valueRegister' => "",
            ]
        ];
        $service_req_id = AledaServiceRequest::insertGetId([
            'method' => "sellDematModeES",
            'payload' => json_encode($params),
            'trans_id' => $trans_id,
            'invoke_at' => date("Y-m-d H:i:s"),
            'invoke_by' => auth()->check() ? auth()->user()->id : "",
            'status' => 0
        ]);
        self::setWsdl(config('aleda.web_service_url'));
        $this->service = InstanceSoapClient::init();
        try {
            $response = $this->service->sellDematModeES($params);
            AledaServiceRequest::where('id',$service_req_id)->update([
                'status' => 1,
                'http_code' => 200,
                'response' => json_encode($response)
            ]);
            $collection = collect($params);
            $filtered = $collection->except(['configId','login','pwd','terminalChecker']);
            Log::info("SellDematModeES params data for User ".auth()->user()->id."_".auth()->user()->username,[$filtered->all()]);
            $res = $response->sellDematESDataHolder;
            $error_check = $response->return;
            if($error_check->resultCode != 0){
                Log::warning("SellDematModeES resultCode Exception => ".$error_check->errorText." ".$error_check->resultCode." product code was ".$product_code);
                throw new \Exception(trans('myservice.unable_to_print'));
            }
            if($res->nextSubType == 120){ // fixed to confirm print card
                sleep(3);
                //confirm ES card
                $confirm_trans_id = ApiHelper::generateTransID();
                $conf_checkOccurrence = AledaServiceRequest::where('method','sellDematModeES')->orderBy('id',"DESC")->first();
                if(collect($conf_checkOccurrence)->count() > 0){
                    $last_time_type_conf =  date("s",strtotime($conf_checkOccurrence->invoke_at));
                    $last_es_conf = $conf_checkOccurrence->trans_id;
                }else{
                    $last_time_type_conf = "start";
                    $last_es_conf = "start";
                }
                $params_conf = [
                    'configId' => config('aleda.config_id'),
                    'login' => config('aleda.login'),
                    'pwd' => config('aleda.password'),
                    'terminalChecker' => config('aleda.terminal'),
                    'transId' =>  str_replace("_".auth()->user()->id, "", $confirm_trans_id),
                    'lastTimeType' => $last_time_type_conf,
                    'subType' => "120", // fixed
                    'productCode' => $product_code,
                    'data' => $res->data,
                    'codeAction' => "00",
                    'lastES' => $last_es,
                    'qryMode' => 0,//fixed
                    'sellDematESDataHolder' => [
                        'operatorMsg' => "",
                        'operatorTransId' => "",
                        'data' => "",
                        'redirect' => "",
                        'nextSubType' => "",
                        'nextMessage' => "",
                        'valueRegister' => "",
                    ]
                ];
                $service_req_id_conf = AledaServiceRequest::insertGetId([
                    'method' => "sellDematModeES",
                    'payload' => json_encode($params_conf),
                    'trans_id' => $confirm_trans_id,
                    'invoke_at' => date("Y-m-d H:i:s"),
                    'invoke_by' => auth()->check() ? auth()->user()->id : "",
                    'status' => 0
                ]);
                $response_conf = $this->service->sellDematModeES($params_conf);
                AledaServiceRequest::where('id',$service_req_id_conf)->update([
                    'status' => 1,
                    'http_code' => 200,
                    'response' => json_encode($response_conf)
                ]);
                $collection_conf = collect($params_conf);
                $filtered_conf = $collection_conf->except(['configId','login','pwd','terminalChecker']);
                Log::info("SellDematModeES Confirm Action params data for User ".auth()->user()->id."_".auth()->user()->username,[$filtered_conf->all()]);
                $res_conf = $response_conf->sellDematESDataHolder;
                $error_check_conf = $response_conf->return;
                if($error_check_conf->resultCode != 0){
                    Log::warning("SellDematModeES Confirm resultCode Exception => ".$error_check_conf->errorText." ".$error_check_conf->resultCode." product code was ".$product_code);
                    throw new \Exception(trans('myservice.unable_to_print'));
                }
                $explode_data = explode('|',$res_conf->data);
                if(collect($explode_data)->count() == 0){
                    Log::warning("SellDematModeES Confirm Data Null Exception =>  product code was ".$product_code,[$res_conf]);
                    throw new \Exception(trans('myservice.unable_to_print'));
                }
                Log::info("SellDematModeES Confirm Success for User ".auth()->user()->id."_".auth()->user()->username." =>  product code was ".$product_code,[$res_conf]);
                return (object)[
                    'secretCode' => $explode_data[0],
                    'serialNb' => $explode_data[2],
                    'validityDate' => ''
                ];
            }else{
                Log::warning("SellDematModeES nextSubType Exception => ".$res->nextSubType);
                throw new \Exception(trans('myservice.unable_to_print'));
            }
        } catch (\Exception $e) {
            Log::warning("Exception while fetching card info ".$e->getMessage());
            AledaServiceRequest::where('id',$service_req_id)->update([
                'http_code' => $e->getCode(),
                'response' => $e->getMessage()
            ]);
            return (object)[
                'error' => $e->getMessage()
            ];
        }
    }
}
