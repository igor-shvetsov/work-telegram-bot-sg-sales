# Php Bot CDK

## Проверка наличия сообщений от бота

```php
use Telegram\Bot\Api;

$token = '...';

$telegram = new Api($token);
$update = $telegram->getWebhookUpdate();

if (!$update->has('message')) {
    echo 'Telegram Sales Bot';
    return;
}
```

## Окно приветствия с кнопкой Старт

```php
// Текст приветствия
$welcomeText = "Добро пожаловать в Softgamings!\n\nМы рады видеть вас здесь. Нажмите кнопку Start, чтобы начать.";

// URL логотипа компании (должен быть доступен публично)
$logoUrl = 'https://st.softgamings.com/uploads/EGR_B2B_logo.png';

$inlineKeyboard = Keyboard::make()
    ->inline()
    ->row(
        [
            Keyboard::inlineButton(['text' => 'Start', 'callback_data' => 'start'])
        ]
    );

// Отправляем фото с подписью и кнопкой
$response = $telegram->sendPhoto([
    'chat_id' => $chatId,
    'photo' => InputFile::create($logoUrl),
    'caption' => $welcomeText,
    'reply_markup' => $inlineKeyboard,
]);
```


## Отправить сообщение пользователю

```php
use Telegram\Bot\Api;

$telegram = new Api($token);

$response = $telegram->sendMessage([
    'chat_id' => $chatId,
    'text' => 'Ваше текстовое сообщение'
]);
```


## Reply-клавиатура (появляется снизу)

### Создание клавиатуры

```php
use Telegram\Bot\Keyboard\Keyboard;

// Создаем клавиатуру
$keyboard = Keyboard::make()
    ->row([
        Keyboard::button('Кнопка 1'),
        Keyboard::button('Кнопка 2'),
    ])
    ->row([
        Keyboard::button('Кнопка 3'),
    ])
    ->setResizeKeyboard(true)  // Автоматически подгонять размер клавиатуры
    ->setOneTimeKeyboard(true); // Скрывать клавиатуру после нажатия

// Отправляем сообщение с клавиатурой
$response = $telegram->sendMessage([
    'chat_id' => $chatId,
    'text' => 'Выберите вариант:',
    'reply_markup' => $keyboard
]);
```

### Обработка клика по кнопке

Обработка: Бот получает сообщение с текстом кнопки

```php
$update = $telegram->getWebhookUpdate();

$message = $update->getMessage();
$messageText = $message->getText();
$chatId = $message->getChat()->getId();

if ($messageText === 'Текст кнопки') {
    // Обработка нажатия
}
```