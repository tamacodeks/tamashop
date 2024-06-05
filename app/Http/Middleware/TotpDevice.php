<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;
use Auth;

class TotpDevice
{
    public function handle($request, Closure $next)
    {
            if (in_array(auth()->user()->group_id, [3,4])) {
                if(auth()->user()->enable_2fa == 0){
                   if(auth()->user()->verify_2fa != 1){
                       return redirect()->to('enable_2fa')->with('message',trans('common.access_violation'))
                           ->with('message_type','warning');
                   }
                }
            }
        return $next($request);
    }

}