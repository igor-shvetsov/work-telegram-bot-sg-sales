<?php

use Illuminate\Support\Facades\Lang;


// В обработчике вебхука добавьте заголовок
$_SERVER['HTTP_XDEBUG_SESSION'] = 'PHPSTORM';

use App\Telegram\SalesBot\TelegramSalesBot;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/bootstrap/redis.php';
require __DIR__ . '/app/bootstrap/db.php';
require __DIR__ . '/app/bootstrap/app.php';
require __DIR__ . '/app/bootstrap/helpers.php';

$test = Lang::get('messages.apples');
$test2 = trans('messages.apples');

new TelegramSalesBot();
