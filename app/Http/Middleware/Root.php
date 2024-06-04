<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;
use Illuminate\Support\Facades\Log;

class Root
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
        if(auth()->user()->group_id != 1){
            Log::warning('User '.auth()->user()->username.' tried to access restricted content!');
            AppHelper::logger('warning','Access Violation','User '.auth()->user()->username.' tried to access restricted content!');
            return redirect('dashboard')->with('message',trans('common.access_violation'))->with('message_type','warning');
        }
        return $next($request);
    }
}
