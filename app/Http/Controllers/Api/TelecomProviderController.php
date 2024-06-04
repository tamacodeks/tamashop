<?php

namespace App\Http\Controllers\Api;

use app\Library\ApiHelper;
use app\Library\AppHelper;
use App\Models\TelecomProviderConfig;
use App\Transformers\TelecomProviderTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TelecomProviderController extends Controller
{
    private $log_title;
    protected $user;

   function __construct()
   {
       parent::__construct();
       $this->log_title = "Telecom Provider API";
       $this->middleware(function ($request, $next) {
           $this->user = \Auth::guard('api')->user();
           if (!$this->user) {
               AppHelper::logger('warning',$this->log_title,"Authentication problem!",request()->all(),true);
               return ApiHelper::response(401,401,"Authentication required");
           }
           return $next($request);
       });
   }

    /**
     * Get List of telecom providers
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function getTelecomProviders(Request $request)
    {
        $telecom_providers = TelecomProviderConfig::all();
        $tp = fractal($telecom_providers, new TelecomProviderTransformer())->toArray();
        return response()->json($tp);
    }

}
