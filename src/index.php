<?php

// В обработчике вебхука добавьте заголовок
$_SERVER['HTTP_XDEBUG_SESSION'] = 'PHPSTORM';

use App\Telegram\SalesBot\TelegramSalesBot;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/bootstrap/redis.php';
require __DIR__ . '/app/bootstrap/db.php';

new TelegramSalesBot();
