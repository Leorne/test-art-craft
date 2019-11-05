<?php

namespace App\Middleware;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class CsrfToken
{
    public function handle(ContainerBuilder $container)
    {
        $csrf = $container->get('csrf');
        return $csrf->checkTokensEqual() ? 200 : 403;
    }
}