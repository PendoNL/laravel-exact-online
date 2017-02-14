<?php

Route::group(['prefix' => 'exact'], function() {
    Route::get('connect', ['as' => 'exact.connect', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appConnect']);
    Route::post('authorize', ['as' => 'exact.authorize', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appAuthorize']);
    Route::get('oauth', ['as' => 'exact.callback', 'uses' => 'PendoNL\LaravelExactOnline\Http\Controllers\LaravelExactOnlineController@appCallback']);
});
