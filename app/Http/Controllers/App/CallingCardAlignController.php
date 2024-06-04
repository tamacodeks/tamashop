<?php

namespace App\Http\Controllers\App;

use app\Library\AppHelper;
use App\Models\TelecomProvider;
use App\Models\TelecomProviderConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CallingCardAlignController extends Controller
{
    private $log_title;
    function __construct()
    {
        parent::__construct();
        $this->log_title = "Calling card align";
    }


    function index(Request $request)
    {
        $this->data['page_title'] = "Calling card align";
        $this->data['telecom_providers'] = TelecomProviderConfig::select('id','name')->get();
        if(!empty($request->input('telecom_provider'))){
            $this->data['cards'] = TelecomProvider::where('tp_config_id',$request->input('telecom_provider'))
                ->orderby('ordering','ASC')->select('id','name','face_value','description')->get();
        }
        $this->data['tp_config_id'] = !empty($request->input('telecom_provider')) ? $request->input('telecom_provider') : '';
        return view('app.cc-align.index',$this->data);
    }

    /**
     * POST - order calling cards
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'reorder' => 'required',
            'tp_config_id' => 'required',
        ]);
        if($validator->fails()){
            AppHelper::logger('warning',$this->log_title,"Validation failed",$request->all());
            return redirect()
                ->back()
                ->with('message',AppHelper::create_error_bag($validator))
                ->with('message_type','warning');
        }
        $cards = json_decode($request->input('reorder'), true);
        $a = 0;
        foreach ($cards as $card) {
            TelecomProvider::where('id',$card['id'])->update([
                'ordering' => $a
            ]);
            $a++;
        }
        $card_info = TelecomProviderConfig::where('id',$request->tp_config_id)->first();
        AppHelper::logger('success',$this->log_title,$card_info->name." ordered successfully",$request->all());
        return redirect()->back()
            ->with('message',trans('common.msg_update_success'))
            ->with('message_type','success');
    }

}
