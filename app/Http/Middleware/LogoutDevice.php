<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;
use Auth;
use Session;

class LogoutDevice
{
    public function handle($request, Closure $next)
    {
        $session_data = \Session::all();
        $user_id = \Session::get('impersonate');
        if(!$user_id){
            if (in_array(auth()->user()->group_id, [3,4])) {
            if(auth()->user()->last_session_id != $session_data['_token']){
                Auth::logout();
                return redirect()->to('/')->with('message',trans('common.access_violation'))
                    ->with('message_type','warning');
            }
            }
        }
        return $next($request);
    }

}