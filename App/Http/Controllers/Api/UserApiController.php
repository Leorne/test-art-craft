<?php

namespace App\Http\Controllers\Api;

use App\Http\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserApiController extends Controller
{
    public function show($type, $key){
        try{
            $user = User::where('key', '=', $key)->firstOrFail()->toArray();
            return ($type === 'json') ? $this->responseJSON($user) : $this->responseXML($user);
        }catch(ModelNotFoundException $e){
            throw new ResourceNotFoundException();
        }
    }

    public function xml($key)
    {
        $user = User::where('key', '=', $key)->firstOrFail()->toArray();
        return $this->responseXML($user);
    }

    public function json($key)
    {
        $user = User::where('key', '=', $key)->firstOrFail();
        return $this->responseJSON($user);
    }
}