<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;
use Auth;

class IpAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user =  Auth::user()->ip_address;
        $user_id = \Session::get('impersonate');
        if (config('app.env') == 'local') {
            $ip_address = \Request::getClientIp(true);
        }
        else
        {
            $ip_address = AppHelper::getIP(true);
        }
        if(Auth::user()->enable_ip == 0)
        {
            if($user != NULL)
            {
                if(!$user_id)
                {
                    if($user != $ip_address){
                        AppHelper::logger('warning','MyService Access violation','User '.$request->user()->username. 'trying to access myservice',$request);
//
                        Auth::logout();
                        return redirect()->to('/')->with('message',trans('common.access_violation'))
                            ->with('message_type','warning');
                    }
                }
            }
        }
        return $next($request);
    }
}
