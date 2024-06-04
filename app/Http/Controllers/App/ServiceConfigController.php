<?php

namespace App\Http\Controllers\App;

use app\Library\AppHelper;
use App\Models\ServiceConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceConfigController extends Controller
{
    private $send_tama_service_id;
    function __construct()
    {
        parent::__construct();
        $this->send_tama_service_id = 1;
    }

    function config_send_tama(){
        $available_countries = array(
            '188' => "Senegal",
            '131' => "Mali",
            '37' => "Cameroon",
            '53' => "Ivorycost",
            '78' => "Gambia",
            '213' => "Togo",
            '90' => "Guinea-Bissau",
        );
        $this->data['avail_countries'] = $available_countries;
        $config = ServiceConfig::where('service_id', $this->send_tama_service_id)->where('type', 'default')->first();
        $this->data['chosen_cty'] = json_decode($config->config,true);
        $this->data['page_title'] = "Manage Send Tama Configuration";
//        dd($this->data);
        return view('app.config.send_tama',$this->data);
    }

    function save_send_tama_config(Request $request){
        $access_data = array();
        if (isset($_POST['selected_country_data']) && count($_POST['selected_country_data']) > 0) {
            foreach ($_POST['selected_country_data'] as $cf_data => $cf_val) {
                $access_data['access_data'][$cf_data] = $cf_val;
            }
            $data['service_id'] = 1;
            $data['config'] = json_encode($access_data);
            $data['type'] = 'default';
            $check_exist = ServiceConfig::where('service_id', '1')->where('type', 'default')->first();
            if (!$check_exist) {
                //insert
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['created_by'] = auth()->user()->id;
                ServiceConfig::insert($data);
                AppHelper::logger('success','Service Config Update Send Tama','Configuration updated');
                return redirect()->back()->with('message', trans('common.msg_update_success'))->with('message_type', 'success');
            } else {
                //update
                $data['updated_at'] = date("Y-m-d H:i:s");
                $data['updated_by'] = auth()->user()->id;
                ServiceConfig::where('service_id', '1')->where('type', 'default')->update($data);
                AppHelper::logger('success','Service Config Update Send Tama','Configuration updated');
                return redirect()->back()->with('message', trans('common.msg_update_success'))->with('message_type', 'success');
            }
        } else {
            $data['service_id'] = 1;
            $data['config_data'] = json_encode($access_data = array());
            $data['type'] = 'default';
            $data['updated_at'] = date("Y-m-d H:i:s");
            $data['updated_by'] = auth()->user()->id;
            ServiceConfig::where('service_id', '1')->where('type', 'default')->update($data);
            AppHelper::logger('success','Service Config Update Send Tama','Configuration updated');
            return redirect()->back()->with('message', trans('common.msg_update_success'))->with('message_type', 'success');
        }
    }
}
