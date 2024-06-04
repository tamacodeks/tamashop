<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Auth;
use Cache;

class LastUserActivity
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
        if(Auth::check()){
            $expires_at = Carbon::now()->addMinutes(10);
            Cache::put('user-is-online-'.Auth::user()->id,true,$expires_at);
        }
        return $next($request);
    }
}
