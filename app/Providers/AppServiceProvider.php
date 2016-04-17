<?php

namespace App\Providers;

use App\Services\ApiFetcher;
use Illuminate\Support\ServiceProvider;
use Auth;
use Config;
use View;

/**
 * Class AppServiceProvider
 * @package App\Providers
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
        $this->app->bind('api.fetcher', function ($app) {
            return new ApiFetcher();
        });


        if (Config::get('app.debug') === true) {
            // $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            // $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            // $this->app->register(\Spatie\Tail\TailServiceProvider::class);
        }
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