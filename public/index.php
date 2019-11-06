<?php

//Start point

//autoload
require __DIR__ . '/../vendor/autoload.php';

//settings
error_reporting(E_ALL);
ini_set('display_errors', '1');
chdir(__DIR__);


use Symfony\Component\HttpFoundation\Request;



$container = require '../configs/Container.php';
$request = Request::createFromGlobals();
$container->set('request', $request);

$app = $container->get('app');
$response = $app->handle($request);

$response->send();
