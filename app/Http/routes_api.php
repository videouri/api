<?php

Route::group([
    'namespace' => 'Api',
    'prefix' => 'api',
    'middleware' => [
        'cors',
    ],
], function () {
    // Content
    Route::group([
        'prefix' => 'content'
    ], function () {
        Route::get('home', 'ContentController@home');
    });

    // Recommendations
    Route::group([
        'prefix' => 'recommendations'
    ], function () {
        Route::get('video/{custom_id}', 'RecommendationController@forVideo');
    });

    // Search
    Route::get('search', 'SearchController@genericSearch');

    // User
    Route::group([
        'namespace' => 'User',
        'prefix' => 'user',
        'middleware' => 'auth',
    ], function () {
        Route::resource('favorites', 'FavoritesController', [
            'only' => ['index', 'store']
        ]);

        Route::resource('watch-later', 'WatchLaterController', [
            'only' => ['index', 'store']
        ]);
    });
});
