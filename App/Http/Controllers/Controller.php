<?php

namespace App\Http\Controllers;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


abstract class Controller
{
    protected $container;
    protected $response;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
        $this->request = Request::createFromGlobals();
        $this->response = (new Response())->prepare($this->request);
    }


    protected function render(string $view, array $parameters = []): Response
    {
        $htmlContent = $this->renderView($view, $parameters);
        return $this->response->setContent($htmlContent);
    }

    protected function renderView(string $view, array $parameters = []): string
    {
        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Twig Bundle is not available. Try running "composer require symfony/twig-bundle".');
        }
        return $this->container->get('twig')
            ->render($view, $parameters);
    }

    protected function redirect(string $path, int $status = 302, $headers = []): Response
    {
        return new RedirectResponse($path, $status, $headers);
    }

    protected function responseJSON($data): Response
    {
        $response = $this->response;
        $response->setContent(json_encode([
            'data' => $data,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    protected function responseXML($data): Response
    {
        $response = $this->response;
        $xml = new \SimpleXMLElement('<data/>');
        foreach ($data as $keys => $value){
            $xml->addChild($keys,$value);
        }
        return $response->setContent($xml->asXML());
    }

    protected function returnStatus(int $status): Response
    {
        return $this->response->setStatusCode($status);
    }
}