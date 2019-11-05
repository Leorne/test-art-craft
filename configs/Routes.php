<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;


/*##################################*/
/* Routes */

$routes = new RouteCollection();

$routes->add('main', new Route(
    '/',
    ['controller' => 'UserController', 'method' => 'index']
));

$routes->add('store', new Route(
    '/register',
    ['controller' => 'UserController', 'method' => 'store'],
    [],[],'',[],'POST'
));

$routes->add('create', new Route(
    '/register',
    ['controller' => 'UserController', 'method' => 'create'],
    [],[],'',[],'GET'
));

$routes->add('show', new Route(
    '/{key}',
    ['controller' => 'UserController', 'method' => 'show', 'middleware' => [
        'Auth'
    ]],
    [],['Auth'],'',[],'GET'
));

$routes->add('userApi', new Route(
    '/userApi/{type}/{key}',
    ['controller' => 'Api\UserApiController', 'method' => 'show', 'middleware' => [
        'Auth'
    ]],
    ['type' => '(xml|json)'],['Auth'],'',[],'GET'
));

/*##################################*/

return $routes;