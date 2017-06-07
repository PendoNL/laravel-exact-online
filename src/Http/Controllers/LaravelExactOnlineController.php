<?php

namespace PendoNL\LaravelExactOnline\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $user->exact_authorisationCode = request()->get('code');
        $user->save();

        $connection = app()->make('Exact\Connection');

        return view('laravelexactonline::connected', ['connection' => $connection]);
    }
}
