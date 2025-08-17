<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'corp-integrations',
    'username'  => 'admin',
    'password'  => 'rNZzq5U37DqJlNe',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Делает Capsule доступным глобально
$capsule->setAsGlobal();

// Включает Eloquent ORM
$capsule->bootEloquent();

// Регистрация фасадов
if (!class_exists('DB')) {
    class_alias(Illuminate\Support\Facades\DB::class, 'DB');
}