<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;

class ParentOnlyMiddleware
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
        if(!in_array(auth()->user()->group_id,[2,3])){
            AppHelper::logger('warning',"Parent Only Middleware","Access violation from ".auth()->user()->username);
            return redirect()
                ->back()
                ->with('message',trans('common.access_violation'))
                ->with('message_type','warning');
        }
        return $next($request);
    }
}
