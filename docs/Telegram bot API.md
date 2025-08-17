# Telegram Bot API

## Данные при отправке боту текстового сообщения

```php
{
  "update_id": 937944386,
  "message": {
    "message_id": 2,
    "from": {
      "id": 302410462,
      "is_bot": false,
      "first_name": "Игорь",
      "last_name": "Швецов",
      "username": "IgorShvetsov",
      "language_code": "ru"
    },
    "chat": {
      "id": 302410462,
      "first_name": "Игорь",
      "last_name": "Швецов",
      "username": "IgorShvetsov",
      "type": "private"
    },
    "date": 1755343710,
    "text": "test"
  }
}

{
  "update_id": 937944387,
  "message": {
    "message_id": 3,
    "from": {
      "id": 302410462,
      "is_bot": false,
      "first_name": "Игорь",
      "last_name": "Швецов",
      "username": "IgorShvetsov",
      "language_code": "ru"
    },
    "chat": {
      "id": 302410462,
      "first_name": "Игорь",
      "last_name": "Швецов",
      "username": "IgorShvetsov",
      "type": "private"
    },
    "date": 1755343967,
    "text": "test2"
  }
}
```

## Выбор продуктов, ответ

```php
{
  "id": "1298843048059040339",
  "from": {
    "id": 302410462,
    "is_bot": false,
    "first_name": "Игорь",
    "last_name": "Швецов",
    "username": "IgorShvetsov",
    "language_code": "ru"
  },
  "message": {
    "message_id": 69,
    "from": {
      "id": 8000600930,
      "is_bot": true,
      "first_name": "MySgSalesBot",
      "username": "MySgSalesBot"
    },
    "chat": {
      "id": 302410462,
      "first_name": "Игорь",
      "last_name": "Швецов",
      "username": "IgorShvetsov",
      "type": "private"
    },
    "date": 1755370593,
    "text": "Выберите продукты (нажмите для выбора/отмены):",
    "reply_markup": {
      "inline_keyboard": [
        [
          {
            "text": "◻️ Продукт Premium",
            "callback_data": "toggle_product_1"
          }
        ],
        [
          {
            "text": "◻️ Продукт Standard",
            "callback_data": "toggle_product_2"
          }
        ],
        [
          {
            "text": "◻️ Продукт Basic",
            "callback_data": "toggle_product_3"
          }
        ],
        [
          {
            "text": "🚀 Завершить выбор",
            "callback_data": "finish_selection"
          }
        ]
      ]
    }
  },
  "chat_instance": "2804501990144952489",
  "data": "toggle_product_1"
}
```