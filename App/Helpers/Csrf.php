<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Csrf
{
    protected $requestToken;
    protected $sessionToken;
    protected $session;
    protected $request;


    public function __construct(Session $session)
    {
        $this->request = Request::createFromGlobals();
        $this->session = $session;
        $this->checkToken();
    }

    public function setToken()
    {
        $this->sessionToken = $this->generateToken();
        $this->session->set('csrf-token', $this->sessionToken);
    }

    public function getToken()
    {
        return $this->sessionToken;
    }

    public function getRequestToken(){
        return $this->requestToken;
    }

    public function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public function checkToken()
    {
        if ($this->session->has('csrf-token')) {
            $this->sessionToken = $this->session->get('csrf-token');
        }else{
            $this->setToken();
        }
        if($this->request->request->has('csrf-token')){
            $this->requestToken = $this->request->request->get('csrf-token');
        }
        return false;
    }

    //
    public function checkTokensEqual()
    {
        //not safe method
        if($this->request->isMethod('POST')){
            return hash_equals($this->sessionToken, $this->requestToken) ? true : false;
        }
        return true;
    }
}