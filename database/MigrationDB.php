<?php

require __DIR__ . '/../vendor/autoload.php';
chdir(__DIR__);

use App\Services\Db;

$db = new Db();

require 'migrations/User.php';