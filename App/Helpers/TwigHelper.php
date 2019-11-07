<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;

class TwigHelper
{
    protected $request;

    public function __construct()
    {
        $request = Request::createFromGlobals();;
        $this->request = $request;
    }

    public function asset($pathname){
        return $this->request->getUriForPath($pathname);
    }
}