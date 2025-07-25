<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use App\Models\UserAccess;
use Closure;

class ServiceAccess
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
        if($request->segment(1) != 'calling-cards'){
            // Fetch the first segment as a string
            $segment = $request->segment(1);

            // If the first segment is 'bus', set the segment to 'flix-bus'
            if ($segment == 'bus') {
                $segment = 'flix-bus';
            }

            // Perform the service access check
            $check_service_access = UserAccess::join('services', 'services.id', 'user_access.service_id')
                ->where('user_access.user_id', auth()->user()->id)
                ->where('services.name', ucwords(str_replace('-', ' ', $segment)))
                ->where('user_access.status', 1)
                ->first();
            if (!$check_service_access) {
                AppHelper::logger('warning', 'Service Access Restricted', 'User ' . $request->user()->username . ' was trying to access ' . $segment);
                return redirect()->back()
                    ->with('message', trans('common.access_violation'))
                    ->with('message_type', 'warning');
            }
        }
        return $next($request);
    }
}
