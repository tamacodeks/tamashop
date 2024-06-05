<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;
use Auth;
use Session;

class TotpDevice
{
    public function handle($request, Closure $next)
    {
        $user_id = \Session::get('impersonate');
        if(!$user_id){
            if (in_array(auth()->user()->group_id, [3,4])) {
                if(auth()->user()->enable_2fa == 0){
                   if(auth()->user()->verify_2fa != 1){
                       return redirect()->to('enable-2fa')->with('message',trans('common.access_totp'))
                           ->with('message_type','warning');
                   }
                }
            }
        }
        return $next($request);
    }

}