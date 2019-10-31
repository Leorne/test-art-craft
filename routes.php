<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('blog_list', new Route('/blog', array(
    '_controller' => [BlogController::class, 'list']
)));

$collection->add('blog_show', new Route('/blog/{slug}', array(
    '_controller' => [BlogController::class, 'show']
)));


return $collection;