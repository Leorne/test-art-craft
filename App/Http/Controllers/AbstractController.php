<?php

namespace App\Http\Controllers;

abstract class AbstractController
{



    /**
     * Returns a rendered view.
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Twig Bundle is not available. Try running "composer require symfony/twig-bundle".');
        }
        return $this->container->get('twig')->render($view, $parameters);
    }
}