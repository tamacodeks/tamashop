<?php

namespace App\Http\Middleware;

use Closure;

class Language
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
        //thanks to -> https://mydnic.be/post/laravel-5-and-his-fcking-non-persistent-app-setlocale for non persistent locale problem
        if (\Session::has('locale')) {
            \App::setLocale(\Session::get('locale'));
        }
        else {
            // This is optional as Laravel will automatically set the fallback language if there is none specified
            \App::setLocale(\Config::get('app.fallback_locale'));
        }
        return $next($request);
    }
}
