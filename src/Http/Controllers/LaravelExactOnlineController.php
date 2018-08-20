<?php

namespace PendoNL\LaravelExactOnline\Http\Controllers;

use Illuminate\Routing\Controller;
use PendoNL\LaravelExactOnline\LaravelExactOnline;

class LaravelExactOnlineController extends Controller
{
    /**
     * Connect Exact app
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appConnect() {
        return view('laravelexactonline::connect');
    }

    /**
     * Authorize to Exact
     * Sends an oAuth request to the Exact App to get tokens
     */
    public function appAuthorize() {
        $connection = app()->make('Exact\Connection');
        $connection->redirectForAuthorization();
    }

    /**
     * Exact Callback
     * Saves the authorisation and refresh tokens
     */
    public function appCallback() {
        $config = LaravelExactOnline::loadConfig();
        $config->exact_authorisationCode = request()->get('code');
        LaravelExactOnline::storeConfig($config);

        $connection = app()->make('Exact\Connection');

        return view('laravelexactonline::connected', ['connection' => $connection]);
    }
}
