<?php

namespace App\Http\Controllers\Api;

use app\Library\ApiHelper;
use app\Library\AppHelper;
use App\Models\UserRateTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    private $log_title;
    protected $user;
    function __construct()
    {
        parent::__construct();
        $this->log_title="Common API Controller";
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::guard('api')->user();
            if (!$this->user) {
                AppHelper::logger('warning',$this->log_title,"Authentication problem!",request()->all(),true);
                return ApiHelper::response(401,401,"Authentication required");
            }
            return $next($request);
        });
    }

    function getHeartBeat(Request $request)
    {
        $user_rate_table = UserRateTable::join('rate_table_groups','rate_table_groups.id','user_rate_tables.rate_group_id')->where('user_rate_tables.user_id',$this->user->id)->select('rate_table_groups.name')->first();
        $res_data = [
            'balance' => AppHelper::getBalance($this->user->id,"EUR",true),
            'ws_balance' => AppHelper::getBalance($this->user->id,"EUR",false),
            'credit_limit' => AppHelper::get_credit_limit($this->user->id),
            'rate_table' => optional($user_rate_table)->name
        ];
        AppHelper::logger('success',$this->log_title,"Heartbeat API fetched",'',true);
        return ApiHelper::response(200,200,"balance fetched",$res_data);
    }
}

