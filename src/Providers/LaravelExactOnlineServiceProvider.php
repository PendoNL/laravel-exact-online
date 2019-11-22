<?php

namespace PendoNL\LaravelExactOnline\Providers;

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

        $this->app->singleton('Exact\Connection', function () {
            $config = LaravelExactOnline::loadConfig();

            $connection = new \Picqer\Financials\Exact\Connection();
            $connection->setRedirectUrl(route('exact.callback'));
            $connection->setExactClientId(config('laravel-exact-online.exact_client_id'));
            $connection->setExactClientSecret(config('laravel-exact-online.exact_client_secret'));
            $connection->setBaseUrl('https://start.exactonline.' . config('laravel-exact-online.exact_country_code'));

            if (config('laravel-exact-online.exact_division') !== '') {
                $connection->setDivision(config('laravel-exact-online.exact_division'));
            }

            if (isset($config->exact_authorisationCode)) {
                $connection->setAuthorizationCode($config->exact_authorisationCode);
            }

            if (isset($config->exact_accessToken)) {
                $connection->setAccessToken(unserialize($config->exact_accessToken));
            }

            if (isset($config->exact_refreshToken)) {
                $connection->setRefreshToken($config->exact_refreshToken);
            }

            if (isset($config->exact_tokenExpires)) {
                $connection->setTokenExpires($config->exact_tokenExpires);
            }

            $connection->setTokenUpdateCallback('\PendoNL\LaravelExactOnline\LaravelExactOnline::tokenUpdateCallback');

            try {
                if (isset($config->exact_authorisationCode)) {
                    $connection->connect();
                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $connection->setAccessToken(null);
                $connection->setRefreshToken(null);
                $connection->connect();
            } catch (\Exception $e) {
                throw new \Exception('Could not connect to Exact: ' . $e->getMessage());
            }

            $config->exact_accessToken = serialize($connection->getAccessToken());
            $config->exact_refreshToken = $connection->getRefreshToken();
            $config->exact_tokenExpires = $connection->getTokenExpires();

            LaravelExactOnline::storeConfig($config);

            return $connection;
        });
    }
}
