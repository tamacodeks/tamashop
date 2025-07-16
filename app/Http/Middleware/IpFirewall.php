<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpFirewall
{
    // List of allowed IPs
    protected $allowedIps = [
        '127.0.0.1',        // local
        '192.168.1.100',    // your network IPs
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->ip(), $this->allowedIps)) {
            abort(403, 'Your IP is not allowed.');
        }

        return $next($request);
    }
}
