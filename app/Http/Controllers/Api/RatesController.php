<?php

namespace App\Http\Controllers\Api;

use app\Library\ApiHelper;
use app\Library\AppHelper;
use App\Models\RateTable;
use App\Models\UserRateTable;
use App\Transformers\RatesTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatesController extends Controller
{
    private $log_title;
    protected $user;
    function __construct()
    {
        parent::__construct();
        $this->log_title="Rates Controller";
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::guard('api')->user();
            if (!$this->user) {
                AppHelper::logger('warning',$this->log_title,"Authentication problem!",request()->all(),true);
                return ApiHelper::response(401,401,"Authentication required");
            }
            return $next($request);
        });
    }


    function getRates()
    {
        $rate_tables = RateTable::join('user_rate_tables','user_rate_tables.rate_group_id','rate_tables.rate_group_id')
                            ->join('calling_cards','calling_cards.id','rate_tables.cc_id')
                            ->where('rate_tables.user_id',$this->user->parent_id)
                            ->where('user_rate_tables.user_id',$this->user->id)
                            ->select([
                                'calling_cards.id',
                                'calling_cards.name',
                                'calling_cards.description',
                                'calling_cards.face_value',
                                'rate_tables.sale_price',
                            ])->get();
        if(!$rate_tables)
        {
            AppHelper::logger('warning',$this->log_title,"No rates table set for user ".$this->user->username);
            return ApiHelper::response('404',200,trans('api.contact_admin'));
        }
        $cards = fractal($rate_tables, new RatesTransformer())->toArray();
        AppHelper::logger('success',$this->log_title,"Rates tables fetched ");
        return response()->json($cards);
    }

}
