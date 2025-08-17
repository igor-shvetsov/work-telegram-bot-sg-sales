# work-telegram-bot-sg-sales

Корпоративные интеграции.


## Привязка вебхука Telegram бота к локальному Docker окружению

### Запуск Docker окружения

```
npm run up
```

php запущен на http://localhost:8002/

### Localtunnel, перенаправление на http://localhost:8002/

Запускаем

```
npm run localtunnel
```

В консоли появится сообщение

```
> work-telegram-bot-sg-sales@1.0.0 localtunnel
> lt --port 8002 --subdomain tg-sg-bot-salest

your url is: https://tg-sg-bot-salest.loca.lt
```

Открываем https://tg-sg-bot-sales.loca.lt

Страница требует авторизации. Получем пароль и вводим, нажимаем отправить.

```
wget -q -O - https://loca.lt/mytunnelpassword

89.23.13.113
```

#### Если docker окружение не было запущено 

После успешного ввода произойдет перенаправление на

https://tg-sg-bot-sales.loca.lt/login

Если Grafana открывается, но требует логин/пароль

По умолчанию в Grafana:

Логин: admin
Пароль: admin (при первом входе попросит сменить).

Меняем логин и пароль на свои.


## Докер

sudo docker-compose exec -it corp-integrations-php bash

docker rmi -f <IMAGE_ID>

### Установка зависимостей через composer

docker-compose run --rm composer require illuminate/redis illuminate/support
docker-compose run --rm composer require predis/predis
docker-compose run --rm composer require illuminate/container
docker-compose run --rm composer require illuminate/database illuminate/events illuminate/support vlucas/phpdotenv

## Telegram Bot

Адрес бота https://web.telegram.org/k/#@MySgSalesBot

Token 8000600930:AAH3EP_MWHAixunVjvNqJKNpXLLpxlJv6JA

### Установка хука

Общий вид

https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://tg-sg-bot-salest.loca.lt/

Подставляем и открываем страницу

https://api.telegram.org/bot8000600930:AAH3EP_MWHAixunVjvNqJKNpXLLpxlJv6JA/setWebhook?url=https://tg-sg-bot-salest.loca.lt/

Ответ должен быть вида

```
{"ok":true,"result":true,"description":"Webhook was set"}
```