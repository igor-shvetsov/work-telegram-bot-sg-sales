# Debug PhpStorm

## Вход в контейнер с php 

```
docker-compose exec -it php bash
```

## Не работает XDebug

Смотрим где находится конфигурация php.ini

```
docker-compose exec php php --ini

Warning: Module "redis" is already loaded in Unknown on line 0
Configuration File (php.ini) Path: /usr/local/etc/php
Loaded Configuration File:         /usr/local/etc/php/php.ini
Scan for additional .ini files in: /usr/local/etc/php/conf.d
Additional .ini files parsed:      /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini,
/usr/local/etc/php/conf.d/docker-php-ext-redis.ini,
/usr/local/etc/php/conf.d/docker-php-ext-sodium.ini,
/usr/local/etc/php/conf.d/docker-php-ext-zip.ini
```

Установка модуля xdebug

```
pecl install xdebug && docker-php-ext-enable xdebug
```

Проверка, что Xdebug установлен (список модулей)

```
docker-compose exec php php -m | grep xdebug
```

Проверяем список дополнительных php.ini

```
ls -l /usr/local/etc/php/conf.d/

total 20
-rw-r--r-- 1 root root 23 Aug 12 22:30 docker-php-ext-opcache.ini
-rw-r--r-- 1 root root 16 Aug 18 07:53 docker-php-ext-redis.ini
-rw-r--r-- 1 root root 17 Aug 12 22:30 docker-php-ext-sodium.ini
-rw-r--r-- 1 root root 22 Aug 18 08:06 docker-php-ext-xdebug.ini
-rw-r--r-- 1 root root 14 Aug 18 07:52 docker-php-ext-zip.ini

```

## Проверка работы XDebug

В PhpStorm нажимем на кнопку "Start listening", ставим точку останова и заходим на страницу

```
http://localhost:8002/?XDEBUG_SESSION=PHPSTORM
```

В IDE должны увидеть, что дошли до нужного участка кода и видны все данные в этот момент времени.
