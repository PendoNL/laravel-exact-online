<?php

namespace PendoNL\LaravelExactOnline;

use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Picqer\Financials\Exact\Connection;

class LaravelExactOnline
{
    private $connection = [];

    /**
     * LaravelExactOnline constructor.
     */
    public function __construct()
    {
        $this->connection = app()->make('Exact\Connection');
    }

    /**
     * Magically calls methods from Picqer Exact Online API
     *
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        if(substr($method, 0, 10) == "connection") {

            $method = lcfirst(substr($method, 10));

            call_user_func([$this->connection, $method], implode(",", $arguments));

            return $this;

        } else {

            $classname = "\\Picqer\\Financials\\Exact\\" . $method;

            if(!class_exists($classname)) {
                throw new \Exception("Invalid type called");
            }

            return new $classname($this->connection);

        }

    }

    public static function tokenUpdateCallback (Connection $connection) {
        $config = self::loadConfig();

        $config->exact_accessToken = serialize($connection->getAccessToken());
        $config->exact_refreshToken = $connection->getRefreshToken();
        $config->exact_tokenExpires = $connection->getTokenExpires();

        self::storeConfig($config);
    }

    public static function loadConfig()
    {
        if(config('laravel-exact-online.exact_multi_user')) {
            return Auth::user();
        } else {
            $config = '{}';

            if (Storage::exists('exact.api.json')) {
                $config = Storage::get(
                    'exact.api.json'
                );
            }

            return (object) json_decode($config, false);
        }
    }

    public static function storeConfig($config)
    {
        if(config('laravel-exact-online.exact_multi_user')) {
            $config->save();
        } else {
            Storage::put('exact.api.json', json_encode($config));
        }
    }

}
