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
    Route::get('login', 'Auth\AuthController@showLoginForm')->name('login.get');
    Route::post('login', 'Auth\AuthController@login')->name('login.post');
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
    Route::get('video/{slug}/{custom_id}', 'VideoController@show');

    // User panel
    Route::group([
        'prefix'    => 'user/{name}',
        'middleware' => 'auth',
        'namespace' => 'User',
    ], function () {
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
