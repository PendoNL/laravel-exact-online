<?php

if(config('laravel-exact-online.exact_multi_user')) {
    Route::group(['middleware' => ['web']], function () {
        Route::group(['prefix' => 'exact', 'middleware' => 'auth'], function() {
            Route::get('connect', ['as' => 'exact.connect', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appConnect']);
            Route::post('authorize', ['as' => 'exact.authorize', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appAuthorize']);
            Route::get('oauth', ['as' => 'exact.callback', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appCallback']);
        });
    });
} else {
    Route::group(['prefix' => 'exact'], function() {
        Route::get('connect', ['as' => 'exact.connect', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appConnect']);
        Route::post('authorize', ['as' => 'exact.authorize', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appAuthorize']);
        Route::get('oauth', ['as' => 'exact.callback', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appCallback']);
    });
}
