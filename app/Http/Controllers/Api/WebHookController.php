<?php

namespace App\Http\Controllers\Api;

use app\Library\ApiHelper;
use app\Library\AppHelper;
use app\Library\DBHelper;
use App\Models\AppCommission;
use App\Models\Log;
use App\Models\Service;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class WebHookController extends Controller
{
    function updateServices(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'service_id' => "required|exists:services,id",
            'status' => "required",
            'commission' => "required"
        ]);
        if($validator->fails())
        {
            \Illuminate\Support\Facades\Log::warning("Webhook validation failed for update!",[$validator->errors()]);
            return AppHelper::response('400', 400, "Validation failed");
        }
        Service::where('id',$request->service_id)->update([
            'status' => $request->status,
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => auth()->user()->id
        ]);
        $old_com = AppCommission::where('service_id',$request->service_id)->first();
        $prev_comm = isset($old_com->commission) ? $old_com->commission : 0;
        AppCommission::where('service_id',$request->service_id)->update([
            'service_id' => $request->service_id,
            'prev_com' => $prev_comm,
            'commission' => $request->commission,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        //remove cache for app commissions
        $accessCacheKey = md5(vsprintf("%s,%s", [
            "appcommission",
            $request->service_id
        ]));
        \Cache::forget($accessCacheKey);
        //fetch all users and change the service access
        $users = User::whereIn('group_id',[2,3,4])->select('id','group_id')->get();
        $collections = collect($users);
        $commission = 0;
        $service = Service::find($request->service_id);
        if($collections->count() > 0){
            foreach ($users as $user) {
                //update service status
                $status = !empty($request->status) ? 1 : 0;
                //update service access
                DBHelper::update_service_access($user->id, $request->service_id, $status);
                $service_accessCache = md5(vsprintf("%s,%s,%s,%s", [
                    $service->name,$user->id,$user->username,$user->group_id
                ]));
                \Cache::forget($service_accessCache);
            }
        }
        $clearCache = \Artisan::call("cache:clear");
        $configCache = \Artisan::call("config:cache");
        \Illuminate\Support\Facades\Log::info("App commission for the service ".$request->service_id.' was updated');
        return ApiHelper::response(200, 200, "App commission was updated!");
    }
}
