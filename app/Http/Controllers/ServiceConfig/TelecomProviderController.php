<?php

namespace App\Http\Controllers\ServiceConfig;

use App\Models\TelecomProviderConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use app\Library\AppHelper;
use App\Models\Country;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class TelecomProviderController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * View Index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $this->data['page_title'] = "Manage Telecom Providers";
        return view('service-config.telecom-providers.index',$this->data);
    }

    /**
     * Ajax Datatables source
     * @param Request $request
     * @return mixed
     */
    function fetch_data(Request $request)
    {
        $query = TelecomProviderConfig::join('countries','countries.id','telecom_providers_config.country_id')->select([
            'telecom_providers_config.id as id','countries.nice_name','telecom_providers_config.name','telecom_providers_config.created_at as created_at','telecom_providers_config.updated_at as updated_at','telecom_providers_config.status as country_status'
        ]);
        $telecom_providers = $query;
        return Datatables::of($telecom_providers)
            ->addColumn('status', function ($telecom_providers) {
                return $telecom_providers->country_status == 1 ? "<span class='label label-success'>".trans('common.lbl_enabled')."</span>" :  "<span class='label label-danger'>".trans('common.lbl_disabled')."</span>";
            })
            ->addColumn('action', function ($telecom_providers) {
                return '<a onclick="AppModal(this.href,\''.trans('common.lbl_edit').' '.$telecom_providers->name.'\');return false;"  href="'.secure_url('tp-config/update/'.$telecom_providers->id).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> '.trans('common.lbl_edit').'</a>&nbsp;&nbsp;<a onclick="AppConfirmDelete(this.href,\''.trans('common.lbl_remove').' '.$telecom_providers->name.'\',\''.trans('common.ask_remove').'\' );return false;" href="'.secure_url('tp-config/remove/'.$telecom_providers->id).'" class="btn btn-xs btn-danger"><i class="fa fa-times-circle"></i> '.trans('common.btn_delete').'</a>';
            })
            ->addColumn('image',function ($telecom_providers){
                $tp_config = TelecomProviderConfig::find($telecom_providers->id);
                $src_img = $tp_config->getMedia('telecom_providers')->first();
                $img = !empty($src_img) ? asset(optional($src_img)->getUrl('thumb')) : asset('images/no_image.png');
                return $img;
            })
            ->rawColumns(['action','status','image'])
            ->make(true);
    }

    /**
     * View Update
     * @param string $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function edit($id='')
    {
        if(!empty($id)){
            $row = TelecomProviderConfig::find($id)->toArray();
        }else{
            $row = AppHelper::renderColumns('telecom_providers_config');
        }
        $this->data['row'] = $row;
        $this->data['countries'] = Country::join('telecom_countries','telecom_countries.country_id','countries.id')->select('countries.id','countries.nice_name')->get();
        return view('service-config.telecom-providers.update',$this->data);
    }

    /**
     * Delete
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function delete($id)
    {
        $telecom_country = TelecomProviderConfig::find($id);
        $telecom_country->delete();
        AppHelper::logger('success','Telecom Providers Config',"Configuration for the ID ".$id.' has been removed');
        return redirect()->back()
            ->with('message',trans('common.msg_remove_success'))
            ->with('message_type','success');
    }

    /**
     * POST Update
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'country_id' => 'required',
            'name' => 'required',
        ]);
        if($validator->fails()){
            AppHelper::logger('warning','Telecom Providers Config','Validation failed',$request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        if($request->id != ''){
            $tp_config_id = $request->id;
            //update
            TelecomProviderConfig::where('id',$tp_config_id)
                ->update([
                    'country_id' => $request->country_id,
                    'name' => $request->name,
                    'status' => !empty($request->status) ? 1 : 0,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => auth()->user()->id,
                    'bimedia_card' => !empty($request->bimedia_card) ? 1 : 0,
                ]);
        }else{
            //insert
            $tp_config_id = TelecomProviderConfig::insertGetId([
                'country_id' => $request->country_id,
                'name' => $request->name,
                'status' => !empty($request->status) ? 1 : 0,
                'created_at' => date("Y-m-d H:i:s"),
                'created_by' => auth()->user()->id,
                'bimedia_card' => !empty($request->bimedia_card) ? 1 : 0,
            ]);
        }
        if($request->hasFile('image')){
            $tp_config = TelecomProviderConfig::find($tp_config_id);
            $fileTmp = $request->file('image');
            $fileName = str_slug($request->name,'_').'.'.$fileTmp->getClientOriginalExtension();
            $tp_config->addMedia($request->file('image'))->usingFileName($fileName)->toMediaCollection('telecom_providers');
        }
        AppHelper::logger('success','Telecom Providers Config','updated successfully',$request->all());
        return redirect('tp-config')->with('message',trans('common.msg_update_success'))
            ->with('message_type','success');
    }

}
