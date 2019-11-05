<?php

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use App\Services\Db;

class Kernel
{
    protected $csrf;
    protected $db;
    protected $routes = [];
    protected $dispatcher;
    protected $container;
    public $session;

    protected $allMiddlewares = [
        'Auth' => '\App\Middleware\Auth',
        'CsrfToken' => '\App\Middleware\CsrfToken'
    ];

    protected $requiredMiddlewares = [
        'CsrfToken'
    ];

    protected $routeMiddlewares = [];


    public function __construct(ContainerBuilder $container, Db $db, Session $session)
    {
        $this->container = $container;
        $this->db = $db;
        $this->session = $session;
//        $this->dispatcher = new EventDispatcher();
        $this->csrf = $container->get('csrf');
    }

    public function handle(Request $request): Response
    {
        $path = $request->getPathInfo();
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);
        try {
            $attributes = $matcher->match($path);
            //middleware
            if ((($result = $this->setMiddlewares($attributes)) instanceof Response)) {
                return $result;
            }
            //controller run
            $controller = $attributes['controller'];
            unset($attributes['controller']);
            if (is_callable($controller)) {
                return call_user_func_array($controller, $attributes);
            } else {
                $controllerObjectName = "\App\Http\Controllers\\" . $controller;
                $controllerActionName = $attributes['method'];
                unset($attributes['method']);
                unset($attributes['_route']);
                $controllerObject = new $controllerObjectName($this->container);
                return call_user_func_array([$controllerObject, $controllerActionName], $attributes);
            }
        } catch (ResourceNotFoundException $e) {
            $page = ($this->container->get('twig'))->render('404.html.twig');
            $response = new Response($page, Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

    public function setRoutes()
    {
        $this->routes = require '../configs/Routes.php';
    }


    public function on($event, $callback)
    {
        $this->dispatcher->addListener($event, $callback);
    }

    protected function setMiddlewares(&$attributes)
    {
        if (isset($attributes['middleware'])) {
            $middlewares = $attributes['middleware'];
            unset($attributes['middleware']);
            $this->routeMiddlewares = $middlewares;
        }
        return $this->callMiddlewares();
    }

    protected function callMiddlewares()
    {
        $middlewares = array_unique(array_merge($this->requiredMiddlewares, $this->routeMiddlewares));
        var_dump($middlewares);
        foreach ($middlewares as $middleware) {
            $middlewareObj = new $this->allMiddlewares[$middleware];
            $middlewareResult = call_user_func_array([$middlewareObj, 'handle'], [$this->container]);
            print_r($this->allMiddlewares[$middleware]);
            if ($middlewareResult instanceof Response) {
                return $middlewareResult;
            } elseif ((is_int($middlewareResult) && ($middlewareResult !== 200))) {
                $page = ($this->container->get('twig'))->render('error.html.twig', ['status' => $middlewareResult]);
                return new Response($page, $middlewareResult);
            }
        }
        return false;
    }
}