<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'exact', 'middleware' => 'auth'], function() {
        Route::get('connect', ['as' => 'exact.connect', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appConnect']);
        Route::post('authorize', ['as' => 'exact.authorize', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appAuthorize']);
        Route::get('oauth', ['as' => 'exact.callback', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appCallback']);
    });
});
