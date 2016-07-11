<?php

namespace Videouri\Http\Middleware;

use Closure;
use Session;

/**
 * @package Videouri\Http\Middleware
 */
class VideouriDefaults
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set user's country value based on his IP
        if (!Session::get('country')) {
            $ip = getUserIPAddress();
            Session::put('country', getUserCountry($ip));
        }

        // Have the family_filter by default set to ON
        if (!Session::get('family_filter')) {
            Session::put('family_filter', true);
        }

        return $next($request);
    }
}
