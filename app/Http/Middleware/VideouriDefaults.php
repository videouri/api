<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use Session;

class VideouriDefaults
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
        #if (Request::server('SERVER_ADDR') != Request::server('REMOTE_ADDR')) {
        #    abort(404);
        #}

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
