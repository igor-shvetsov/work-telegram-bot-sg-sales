<?php

use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Facade;

// Создаем контейнер
$app = new Container();

// Устанавливаем контейнер для фасадов
Facade::setFacadeApplication($app);

// Загружаем конфигурацию
if (file_exists(__DIR__.'/../../config/app.php')) {
    $config = require __DIR__.'/../../config/app.php';
    $app->instance('config', new Repository($config));
} else {
    // Конфиг по умолчанию
    $app->instance('config', new Repository([
        'locale' => 'en',
        'fallback_locale' => 'en'
    ]));
}

// Регистрируем файловую систему
$app->singleton('files', function () {
    return new Illuminate\Filesystem\Filesystem();
});

// Регистрируем загрузчик переводов
$app->singleton('translation.loader', function ($app) {
    return new Illuminate\Translation\FileLoader(
        $app['files'],
        __DIR__.'/../../lang'
    );
});

// Регистрируем переводчик
$app->singleton('translator', function ($app) {
    $loader = $app['translation.loader'];
    $locale = $app['config']->get('app.locale', 'en');

    $translator = new Illuminate\Translation\Translator($loader, $locale);
    $translator->setFallback($app['config']->get('app.fallback_locale', 'en'));

    return $translator;
});

return $app;