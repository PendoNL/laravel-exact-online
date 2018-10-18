<?php

namespace PendoNL\LaravelExactOnline;

use Illuminate\Support\Facades\Facade;

class LaravelExactOnlineFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-exact-online';
    }
}
