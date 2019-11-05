<?php

//Start point

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


chdir(__DIR__);

$container = require '../configs/Container.php';


$request = Request::createFromGlobals();
$container->set('request', $request);

$app = $container->get('app');
$session = new Session();
$response = $app->handle($request);

$response->send();
