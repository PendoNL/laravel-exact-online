<?php

namespace PendoNL\LaravelExactOnline\Providers;

use File;
use Illuminate\Support\ServiceProvider;
use PendoNL\LaravelExactOnline\LaravelExactOnline;

class LaravelExactOnlineServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        $this->loadViewsFrom(__DIR__.'/../views', 'laravelexactonline');

        $this->publishes([
            __DIR__.'/../views' => base_path('resources/views/vendor/laravelexactonline'),
            __DIR__.'/../exact.api.json' => storage_path('exact.api.json'),
            __DIR__.'/../config/laravel-exact-online.php' => config_path('laravel-exact-online.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias(LaravelExactOnline::class, 'laravel-exact-online');

        $this->app->singleton('Exact\Connection', function() {
            $config = json_decode(
                File::get(
                    $file = storage_path('exact.api.json')
                ),
                true
            );

            $connection = new \Picqer\Financials\Exact\Connection();
            $connection->setRedirectUrl(route('exact.callback'));
            $connection->setExactClientId(config('laravel-exact-online.exact_client_id'));
            $connection->setExactClientSecret(config('laravel-exact-online.exact_client_secret'));

            if(isset($config['authorisationCode'])) {
                $connection->setAuthorizationCode($config['authorisationCode']);
            }
            if(isset($config['accessToken'])) {
                $connection->setAccessToken(unserialize($config['accessToken']));
            }
            if(isset($config['refreshToken'])) {
                $connection->setRefreshToken($config['refreshToken']);
            }
            if(isset($config['tokenExpires'])) {
                $connection->setTokenExpires($config['tokenExpires']);
            }

            try {

                if(isset($config['authorisationCode'])) {
                    $connection->connect();
                }

            } catch (\Exception $e)
            {
                throw new \Exception('Could not connect to Exact: ' . $e->getMessage());
            }

            $config['accessToken'] = serialize($connection->getAccessToken());
            $config['refreshToken'] = $connection->getRefreshToken();
            $config['tokenExpires'] = $connection->getTokenExpires();

            File::put($file, json_encode($config));

            return $connection;
        });
    }
}
