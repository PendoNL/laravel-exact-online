<?php

namespace PendoNL\LaravelExactOnline\Http\Controllers;

use File;
use App\Http\Controllers\Controller;

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
        $config = json_decode(
            File::get(
                $file = storage_path('exact.api.json')
            ),
            true
        );

        $config['authorisationCode'] = request()->get('code');

        File::put($file, json_encode($config));

        $connection = app()->make('Exact\Connection');

        return view('laravelexactonline::connected', ['connection' => $connection]);
    }
}
