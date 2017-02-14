<?php

namespace PendoNL\LaravelExactOnline;

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

}
