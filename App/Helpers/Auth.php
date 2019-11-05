<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;
use App\Http\Models\User;

class Auth
{
    protected $authorized = false;
    protected $request;
    protected $key;
    protected $user = false;
    protected $auth = false;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        (!$this->setAuthStatus()) ?: $this->setUserData();
    }

    protected function setAuthStatus()
    {
        if ($this->request->cookies->has('user')) {
            $this->key = $this->request->cookies->get('user');
            $this->authorized = true;
            return true;
        }
        return false;
    }

    protected function setUserData(){
        $user = User::where('key', '=', $this->key)->get()->toArray();
        $this->user = $user;
    }

    public function isAuth()
    {
        return $this->authorized;
    }

    public function auth()
    {
        return $this->user;
    }
}