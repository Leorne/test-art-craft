<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;
use App\Http\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function index(){
        $users = User::all();
//        $users = $users->toJson();

        $response = new Response($users);
        return $response->setContent();
    }
}