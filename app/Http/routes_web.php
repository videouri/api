<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/', [
        'as'   => 'home',
        'uses' => 'PagesController@home',
    ]);

    /////////////////
    // Auth routes //
    /////////////////
    // Authentication Routes...
    Route::get('login', 'Auth\AuthController@showLoginForm');
    Route::post('login', 'Auth\AuthController@login');
    Route::get('login/{provider}', 'Auth\AuthController@redirectToProvider');
    Route::get('login/{provider}/callback', 'Auth\AuthController@handleProviderCallback');
    Route::get('logout', 'Auth\AuthController@logout');

    // Registration Routes...
    Route::get('register', 'Auth\AuthController@showRegistrationForm');
    Route::post('register', 'Auth\AuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\PasswordController@reset');

    ///////////////////
    // Public search //
    ///////////////////
    Route::get('search', [
        'as'   => 'search',
        'uses' => 'PagesController@search',
    ]);

    //////////////////
    // Static pages //
    //////////////////
    Route::get('info/{view}/{part?}', 'PagesController@info');

    ////////////////
    // Video page //
    ////////////////
    Route::get('video', 'VideoController@index');
    Route::get('video/{id}/{videoSlug?}', 'VideoController@show');

    ////////////
    // Topics //
    ////////////
    Route::group(['prefix' => 'topic'], function () {
        Route::get('music', [
            'as'   => 'topic.music',
            'uses' => 'TopicsController@music',
        ]);

        Route::get('sports', [
            'as'   => 'topic.sports',
            'uses' => 'TopicsController@sports',
        ]);

        Route::get('trailers', [
            'as'   => 'topic.trailers',
            'uses' => 'TopicsController@trailers',
        ]);

        Route::get('news', [
            'as'   => 'topic.news',
            'uses' => 'TopicsController@news',
        ]);

        Route::get('best-of-week', [
            'as'   => 'topic.best-of-week',
            'uses' => 'TopicsController@bestOfWeek',
        ]);
    });

    // User panel
    Route::group([
        'prefix'    => 'user/{name}',
        'middleware' => 'auth',
        'namespace' => 'User',
    ], function () {
        // Route::resource('profile', 'ProfileController', [
        //     'only' => [
        //         'index',
        //     ],
        // ]);
        //
        // Route::resource('settings', 'SettingsController', [
        //     'only' => [
        //         'index',
        //     ],
        // ]);

        Route::resource('history', 'HistoryController', [
            'only' => [
                'index',
                'show',
            ],
        ]);

        Route::resource('favorites', 'FavoritesController', [
            'only' => [
                'index',
            ],
        ]);

        Route::resource('watch-later', 'WatchLaterController', [
            'only' => [
                'index',
            ],
        ]);
    });
});
