<?php

namespace Videouri\Providers;

use Illuminate\Support\ServiceProvider;
use Response;

/**
 * @package Videouri\Providers
 */
class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data) {
            return Response::json([
                'errors' => false,
                'data' => $data
            ]);
        });

        Response::macro('error', function ($message, $status = 400) {
            return \Response::json([
                'message' => $status . ' error',
                'errors' => [
                    // 'message' => [$message]
                    'message' => $message
                ],
                'status_code' => $status
            ], $status);
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
