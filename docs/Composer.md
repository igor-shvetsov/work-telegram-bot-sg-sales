# Composer

## Нет доступа к нужным классам, фатальные ошибки

1. Смотрим наличие namespace в файле vendor/composer/autoload_classmap.php

2. Другой вариант, выполняем npm run composer-autoload

3. Если отсутствуют нужные пакеты, нужно выполнить установку как в примере ниже 

```php
docker-compose run --rm composer require illuminate/redis illuminate/support
docker-compose run --rm composer require predis/predis
docker-compose run --rm composer require illuminate/container
```