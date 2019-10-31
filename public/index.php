<?php

//Start point

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Specify our Twig templates location
$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');

// Instantiate our Twig
$twig = new Twig_Environment($loader);




$request = Request::createFromGlobals();

$app = new Kernel();

$routes = new RouteCollection();

$routes->add('main', new Route(
    '/',
    array('controller' => 'UserController', 'method' => 'index'),
    array('id' => '[0-9]+')
));

$routes->add('main', new Route(
    '/',
    array('controller' => 'UserController', 'method' => 'index'),
    array('id' => '[0-9]+')
));

$routes->add('main', new Route(
    '/',
    array('controller' => 'UserController', 'method' => 'index'),
    array('id' => '[0-9]+')
));



$app->setRoutes($routes);


$response = $app->handle($request);
$response->send();
