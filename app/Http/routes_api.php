<?php

Route::group([
    'namespace' => 'Api',
    'prefix' => 'api',
    'middleware' => [
        'cors',
    ],
], function () {
    Route::controller('videos', 'VideosController');

    Route::group(['middleware' => 'auth'], function () {
        Route::controller('history', 'HistoryController');
        Route::controller('user', 'UserController');
    });
});
