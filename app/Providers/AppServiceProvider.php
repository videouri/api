<?php

namespace Videouri\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Videouri\Services\Scout\Scout;
use View;

/**
 * @package Videouri\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $currentUser = 'guest';
            if (Auth::user()) {
                $currentUser = Auth::user()->username;
            }

            $view->with('currentUser', $currentUser);
        });
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        if (class_exists('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton('videouri.scout', function () {
            return new Scout();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        //
    }
}
