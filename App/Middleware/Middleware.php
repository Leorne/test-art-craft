<?php

namespace App\Middleware;

class Middleware
{
    public $middlewares = [];
    public $middlewaresTongle = [];

    public function __construct()
    {
        $this->middlewares = require '../configs/middlewares.php';
    }


    public function addMiddleware($name, $callback)
    {
        $this->middlewaresTongle = [$callback];
    }
}