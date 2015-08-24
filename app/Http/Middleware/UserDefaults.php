<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class UserDefaults
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
        // Set user's country value based on his IP
        if (!Session::get('country')) {
            Session::put('country', getUserCountry());
        }

        // Have the family_filter by default set to ON
        if (!Session::get('family_filter')) {
            Session::put('family_filter', true);
        }

        return $next($request);
    }
}
