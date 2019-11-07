<?php

namespace App\Middleware;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Auth
{
    public function handle(ContainerBuilder $container){
        $auth = $container->get('auth');
        return $auth->isAuth() ? 200 : new RedirectResponse('/');
    }
}
