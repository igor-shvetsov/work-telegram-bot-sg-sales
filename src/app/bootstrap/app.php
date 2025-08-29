<?php

use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Facade;

// Загружаем переменные окружения (если используется .env)
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
//$dotenv->load();

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

//// Регистрируем VaultServiceProvider вручную
//$vaultProvider = new Mdma4d\Vault\VaultServiceProvider($app);
//$vaultProvider->register(); // Вызываем метод register для привязки сервисов
//// При необходимости вызовите $vaultProvider->boot() позже, после полной инициализации
//
//// Пытаемся загрузить конфигурацию из Vault
//try {
//    // Если в пакете есть метод boot, который запускает загрузку, вызываем его
//    $vaultProvider->boot();
//
//    // ИЛИ если пакет предоставляет сервис 'vault', можем использовать его
////    if ($app->bound('vault')) {
////        $vaultService = $app->make('vault');
////        // Предположим, что метод getConfig возвращает массив конфигурации из Vault
////        $vaultConfig = $vaultService->getConfig();
////        // Объединяем с существующей конфигурацией
////        $currentConfig = $app['config']->all();
////        $newConfig = array_merge($currentConfig, $vaultConfig);
////        $app->instance('config', new Repository($newConfig));
////    }
//} catch (Exception $e) {
//    // Обработка ошибок подключения к Vault
//    error_log('Vault config loading failed: ' . $e->getMessage());
//}

//function trans($key, $replace = [])
//{
//    global $app;
//
//    return $app['translator']->get($key, $replace);
//}

// Делаем контейнер глобально доступным
global $app;

return $app;
