<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Container\Container;
use Illuminate\Redis\RedisManager;

// Создаём контейнер (нужен для фасадов Laravel)
$container = new Container();

// Настройки Redis (аналогично bootstrap/database.php-v1 в Laravel)
$config = [
    'client' => 'predis', // или 'phpredis'
    'default' => [
        // 'host' => '127.0.0.1',
        'host' => 'redis',
        'port' => 6379, // должен соответствовать в docker-compose.yaml
        'database' => 0,
    ],
];

// Создаём менеджер Redis
$redisManager = new RedisManager($container, $config['client'], $config);

// Регистрируем фасад Redis
Redis::setFacadeApplication($container);
$container->singleton('redis', function () use ($redisManager) {
    return $redisManager;
});

// Теперь можно использовать Redis::фасад()
//Redis::set('step', 'testKey');
//$value = Redis::get('step');
//
//echo $value;
