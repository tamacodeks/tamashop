<?php

namespace App\Http\Middleware;

use Closure;

class RestrictManager
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
        if(auth()->user()->group_id == 3)
        {
            return redirect('dashboard')->with([
                'message' => "Service unavailable!",
                'message_type' => "warning"
            ]);
        }
        return $next($request);
    }
}
