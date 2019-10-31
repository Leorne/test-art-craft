<?php

namespace App;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RouterHelper
{
    protected $routes;

    public function __construct(Kernel $app)
    {
        $this->routes = new RouteCollection();
        $this->setAllRoutes();
        $app->setRoutes($this->getAllRoutes());
    }

    public function setAllRoutes()
    {
        $routes = $this->routes;

        $routes->add('main', new Route(
            '/',
            array('controller' => 'MainController', 'method' => 'load'),
            array('id' => '[0-9]+')
        ));
    }

    public function getAllRoutes()
    {
        return $this->routes;
    }
}