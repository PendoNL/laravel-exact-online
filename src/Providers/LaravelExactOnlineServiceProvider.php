<?php

namespace PendoNL\LaravelExactOnline\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
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

            $user = Auth::user();

            $connection = new \Picqer\Financials\Exact\Connection();
            $connection->setRedirectUrl(route('exact.callback'));
            $connection->setExactClientId(config('laravel-exact-online.exact_client_id'));
            $connection->setExactClientSecret(config('laravel-exact-online.exact_client_secret'));
            $connection->setBaseUrl('https://start.exactonline.' . config('laravel-exact-online.exact_country_code'));

            if(isset($user->exact_authorisationCode)) {
                $connection->setAuthorizationCode($user->exact_authorisationCode);
            }
            if(isset($user->exact_accessToken)) {
                $connection->setAccessToken(unserialize($user->exact_accessToken));
            }
            if(isset($user->exact_refreshToken)) {
                $connection->setRefreshToken($user->rexact_efreshToken);
            }
            if(isset($user->exact_tokenExpires)) {
                $connection->setTokenExpires($user->exact_tokenExpires);
            }

            try {

                if(isset($user->exact_authorisationCode)) {
                    $connection->connect();
                }

            } catch (\Exception $e)
            {
                throw new \Exception('Could not connect to Exact: ' . $e->getMessage());
            }

            $user->exact_accessToken = serialize($connection->getAccessToken());
            $user->exact_refreshToken = $connection->getRefreshToken();
            $user->exact_tokenExpires = $connection->getTokenExpires();

            $user->save();

            return $connection;
        });
    }
}
