<?php

namespace App\Http\Controllers;

use app\Library\AppHelper;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(\Auth::check()){
                //set user's last activity
                User::where('id',\Auth::user()->id)->update([
                    'last_activity' => date("Y-m-d H:i:s")
                ]);
            }
            return $next($request);
        });
    }
}
