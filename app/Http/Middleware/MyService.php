<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;

class MyService
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
        if(in_array(auth()->user()->group_id,[2,3,5]) == false){
            AppHelper::logger('warning','MyService Access violation','User '.$request->user()->username. 'trying to access myservice',$request);
            return redirect()->back()->with('message',trans('common.access_violation'))
                ->with('message_type','warning');
        }
        return $next($request);
    }
}
