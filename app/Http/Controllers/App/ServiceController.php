<?php

namespace App\Http\Controllers\App;

use app\Library\AppHelper;
use app\Library\DBHelper;
use App\Models\AppCommission;
use App\Models\Service;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ServiceController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * View All Services
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(){
        $this->data = [
            'page_title' => "Manage Services"
        ];
        return view('app.services.index',$this->data);
    }

    /**
     * Ajax - Render services into data table
     * @param Request $request
     * @return mixed
     */
    function render_services(Request $request){
        $query = Service::select([
            'id','name','description','created_at','updated_at'
        ]);
        $services = $query;
        return Datatables::of($services)
            ->addColumn('action', function ($services) {
                return '<a onclick="AppModal(this.href,\''.trans('common.lbl_edit').' '.$services->name.'\');return false;"  href="'.secure_url('service/update/'.$services->id).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> '.trans('common.lbl_edit').'</a>&nbsp;&nbsp;<a onclick="AppConfirmDelete(this.href,\''.trans('common.lbl_remove').' '.$services->name.'\',\''.trans('common.ask_remove').'\' );return false;" href="'.secure_url('service/remove/'.$services->id).'" class="btn btn-xs btn-danger"><i class="fa fa-times-circle"></i> '.trans('common.btn_delete').'</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Add or Update Service
     * @param string $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function edit($id=''){
        if(!empty($id)){
            $row = Service::find($id)->toArray();
        }else{
            $row = AppHelper::renderColumns('services');
        }
        $this->data['row'] = $row;
        return view('app.services.update',$this->data);
    }

    /**
     * Update Service
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required'
        ]);
        if($validator->fails()){
            AppHelper::logger('warning','Service Validation Failed','Unable to update tha service info',$request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        $to_update = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => !empty($request->status) ? 1 : 0,
            'order_notification' => !empty($request->order_notification) ? 1 : 0,
        ];
        if(!empty($request->id)){
            $to_update['updated_at'] = date('Y-m-d H:i:s');
            $to_update['updated_by'] = auth()->user()->id;
            Service::where('id',$request->id)->update($to_update);
            $service_id = $request->id;
        }else{
            $to_update['created_at'] = date('Y-m-d H:i:s');
            $to_update['created_by'] = auth()->user()->id;
            $service_id = Service::insertGetId($to_update);
        }
        //fetch all users and change the commission
        $users = User::whereIn('group_id',[2,3,4])->select('id','group_id')->get();
        $collections = collect($users);
        $commission = 0;
        if($collections->count() > 0){
            foreach ($users as $user) {
                //update service status
                $status = !empty($request->status) ? 1 : 0;
                //update service access
                DBHelper::update_service_access($user->id, $service_id, $status);
            }
        }
        AppHelper::logger('success','Service Updated','Service ID '.$service_id.' updated successfully');
        return redirect('services')
            ->with('message',trans('common.msg_update_success'))
            ->with('message_type','success');
    }

    /**
     * Delete Service
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function delete($id){
        $service = Service::find($id);
        $service->delete();
        AppHelper::logger('success',"Service Delete","Service ID $id has been deleted");
        return redirect()->back()->with('message',trans('common.msg_remove_success'))
            ->with('message_type','success');
    }

}
