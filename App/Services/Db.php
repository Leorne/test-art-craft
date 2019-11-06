<?php

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;

class Db
{
    public $capsule;
    public $config;

    public function __construct()
    {
        $this->config = require '../configs/db.php';
        $this->capsule = new Capsule;

        $this->connect();
        $this->boot();
    }

    public function connect()
    {
        $this->capsule->addConnection([
            'driver' => $this->config['DRIVER'],
            'host' => $this->config['HOST'],
            'database' => $this->config['DATABASE'],
            'username' => $this->config['USER_NAME'],
            'password' => $this->config['PASSWORD'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $this->capsule->setAsGlobal();
    }

    public function boot()
    {
        $this->capsule->bootEloquent();
    }

    public function createDatabase(){
        $name = $this->config['DATABASE'];
        $this->capsule->getConnection()->statement('CREATE DATABASE :name', ['name' => $name]);
    }
}