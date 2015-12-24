<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use View;

class CommonViewVariablesProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function($view)
        {
            $currentUser = 'guest';
            if (Auth::user()) {
                $currentUser = Auth::user()->username;
            }

            $view->with('currentUser', $currentUser);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
