<?php

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

//use App\RouterHelper as RouterHelp;

class Kernel implements HttpKernelInterface
{
    const ROOT_DIR = __DIR__ . '/../';

    protected $capsule;
    protected $routes = [];
    protected $dispatcher;


    public function __construct()
    {
        $this->capsule = new Capsule();
        $this->databaseRun();
        $this->dispatcher = new EventDispatcher();

    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        $path = $request->getPathInfo();
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $attributes = $matcher->match($path);
            $controller = $attributes['controller'];
            unset($attributes['controller']);
            if (is_callable($controller)) {
                return call_user_func_array($controller, $attributes);
            } else {
                var_dump($attributes);
                echo "<br>";
                echo "<br>";
                $controllerObjectName = "\App\Http\Controllers\\" . $controller;
                $controllerActionName = $attributes['method'];
                unset($attributes['method']);
                $controllerObject = new $controllerObjectName();
                $response = $controllerObject->$controllerActionName();
                return $response;
            }
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not found', Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

    public function setRoutes(RouteCollection $collection)
    {
        $this->routes = $collection;
    }

    public function on($event, $callback)
    {
        $this->dispatcher->addListener($event, $callback);
    }

    public function databaseRun()
    {
        $this->capsule->addConnection([

            "driver" => "mysql",

            "host" => "127.0.0.1",

            "database" => "air_craft",

            "username" => "admin",

            "password" => "admin"

        ]);
        //Make this Capsule instance available globally.
        $this->capsule->setAsGlobal();
        // Setup the Eloquent ORM.
        $this->capsule->bootEloquent();
    }
}