<?php

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Http\Controllers\Controller;
use App\Services\Db;
use Gregwar\Captcha\CaptchaBuilder;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\AcceptHeader;
use App\Kernel;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


/* Container*/
/*##################################*/

$container = new ContainerBuilder();


//HttpFoundation
$container->register('request', Request::class);
$container->register('response', Response::class);
$container->register('session', Session::class)
    ->addMethodCall('start');

$container->register('cookie', Cookie::class);
$container->register('auth', Auth::class);

//Database
$container->register('db', Db::class);

//Captcha
$container->register('captcha', CaptchaBuilder::class)
    ->addMethodCall('build');

//Twig
$container->register('twig.loader', FilesystemLoader::class)
    ->addArgument('../resources/view');
$container->register('twig', Environment::class)
    ->addArgument(new Reference('twig.loader'))
    ->addMethodCall('addGlobal', ['session', new Reference('session')])
    ->addMethodCall('addGlobal', ['captcha', new Reference('captcha')])
    ->addMethodCall('addGlobal', ['auth', new Reference('auth')]);

//AbstractController
$container->register('controller', Controller::class)
    ->addArgument($container)
    ->setBindings([
        '$container' => $container,
    ]);

$container->register('csrf', Csrf::class)
    ->addArgument(new Reference('session'));

$container->register('auth', Auth::class)
    ->addArgument(new Reference('request'));

//Kernel app
$container->register('app', Kernel::class)
    ->addMethodCall('setRoutes')
    ->addArgument($container)
    ->addArgument(new Reference('db'))
    ->addArgument(new Reference('session'))
    ->setBindings([
        '$container' => $container,
        '$db' => new Reference('db'),
        '$session' => new Reference('session')
    ]);

return $container;
