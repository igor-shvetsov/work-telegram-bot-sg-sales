<?php

/**
 * Бот для отдела продаж в Telegram
 */

namespace App\Telegram\SalesBot;

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\FileUpload\InputFile;

use Illuminate\Support\Facades\Redis;

use App\Telegram\SalesBot\StartCommand;

class TelegramSalesBot {

    private \Telegram\Bot\Api $telegram;
    private \Telegram\Bot\Objects\Update $update;
    private  \Illuminate\Support\Collection $message;
    private $from;
    private $callbackQuery;
    private $callbackQueryData;
    private $chatId;
    private $text;

    /**
     * Данные для хранения выбора пользователя в процессе прохождения шагов общения с ботов
     *
     * @var array
     */
    private array $userSelection = [
        'userInfo' => [
            'userId' => null,
            'firstName' => null,
            'lastName' => null,
            'username' => null,
            'languageCode' => null,
            'chatId' => null,
        ],
        'contactDetails' => null,
        'products' => [
            'selected' => null,
            // свой вариант запрашиваемого продукта или услуги
            'custom' => null,
        ],
        // указание пользователем страны
        'targetCountry' => [
            'predefined' => '',
            'custom' => '',
        ],
        'additionalComment' => '',
    ];

    public function __construct()
    {
        $token = '8000600930:AAH3EP_MWHAixunVjvNqJKNpXLLpxlJv6JA';
        $this->telegram = new Api($token);

        $this->telegram->addCommands([
            StartCommand::class,
        ]);

        // обработка команд
        $this->telegram->commandsHandler(true);

        $this->update = $this->telegram->getWebhookUpdate();

        // error_log(json_encode($this->update['callback_query']));

        // Если не обращение от бота
        if (!$this->update->has('message') && !$this->update->has('callback_query')) {
            echo 'Telegram Sales Bot';
            return;
        }

        $this->message = $this->update->getMessage();
        $this->callbackQuery = $this->update->getCallbackQuery();
        $this->callbackQueryData = !empty($this->callbackQuery) ? $this->callbackQuery->getData() : null;
        $this->chatId = $this->message->getChat()->getId();
        $this->text = $this->message->getText();
        $this->from = $this->message->getFrom();

        // $userSelection = json_decode(Redis::get("user:{$this->chatId}:selection"), true);

        $userSelection = Redis::get("user:{$this->chatId}:selection");

        error_log('User selection from cache: ' . json_encode($this->userSelection));

        if (!empty($data)) {
            $this->userSelection = json_decode($userSelection, true);
        }

        $step = $this->getStep($this->chatId);

        error_log($step);


//        if ($this->update->has('callback_query')) {
//            $data = $this->update->getCallbackQuery()->getData();
//            $chatId = $this->update->getMessage()->getChat()->getId();
//
//            if ($data === 'start') {
//
//            }
//        }

        if (!$this->hasStep($this->chatId)) {
            if ($this->isStartCommand()) {
                $this->setUserInfo();
            }
            $this->setStep($this->chatId, 1);
        } else if ($this->callbackQueryData === 'start') {
            $this->handleStepConcactDetails(true);
        } else if ($step == 1) {
            $this->handleStepConcactDetails();
        } else if ($step == 2) {
            $this->handleStepProducts();
        } else if ($step == 2.2) {
            $this->handleStepCustomProduct();
        }

        Redis::set("user:{$this->chatId}:selection", json_encode($this->userSelection), 'EX', 3600);

        error_log(json_encode($this->userSelection));

        return;

        // $userSelection = Redis::hgetall("user:{$this->chatId}:selection1");

//        if (!empty($userSelection)) {
//            $this->userSelection = $userSelection;
//        }

        // $this->sendMessage();

        // $this->handleFirstStep();
        // $this->handleThirdStep();

        error_log('User text: ' . $this->text);
    }

    private function isStartCommand(): bool {
        return !empty($this->text) && $this->text === '/start';
    }

    private function setUserInfo(): void {
        if (!empty($this->from)) {
            $this->userSelection['userInfo']['userId'] = $this->from->getId();
            $this->userSelection['userInfo']['firstName'] = $this->from->getFirstName();
            $this->userSelection['userInfo']['lastName'] = $this->from->getLastName();
            $this->userSelection['userInfo']['username'] = $this->from->getUsername();
            $this->userSelection['userInfo']['languageCode'] = $this->from->getLanguageCode();
        }

        if (!empty($this->chatId)) {
            $this->userSelection['userInfo']['chatId'] = $this->chatId;
        }
    }

    /**
     * Проверяет есть ли установленный шаг пользователя
     */
    private function hasStep(int $chatId): bool
    {
        $step = Redis::get("user:{$chatId}:step");
        return !empty($step);
    }

    /**
     * Получает текущий шаг пользователя
     */
    private function getStep(int $chatId): int
    {
        $step = Redis::get("user:{$chatId}:step");
        return !empty($step) ? (int)$step : 1;
    }

    /**
     * Устанавливает шаг пользователя
     */
    private function setStep(int $chatId, int $step): void
    {
        Redis::set("user:{$chatId}:step", $step);
    }

    /**
     * Отправить пользователю сообщение
     */
    private function sendMessage(string $message) {
        $response = $this->telegram->sendMessage([
            'chat_id' => $this->chatId,
            'text' => $message,
        ]);
    }

//    /**
//     * Окно приветствия
//     *
//     * @return void
//     */
//    private function handleFirstStep(): void {
//        // Текст приветствия
//        $welcomeText = "Добро пожаловать в Softgamings!\n\nМы рады видеть вас здесь. Нажмите кнопку Start, чтобы начать.";
//
//        // URL логотипа компании (должен быть доступен публично)
//        $logoUrl = 'https://st.softgamings.com/uploads/EGR_B2B_logo.png';
//
//        $inlineKeyboard = Keyboard::make()
//            ->inline()
//            ->row(
//                [
//                    Keyboard::inlineButton(['text' => 'Start', 'callback_data' => 'start'])
//                ]
//            );
//
//        // Отправляем фото с подписью и кнопкой
//        $response = $this->telegram->sendPhoto([
//            'chat_id' => $this->chatId,
//            'photo' => InputFile::create($logoUrl),
//            'caption' => $welcomeText,
//            'reply_markup' => $inlineKeyboard,
//        ]);
//    }

    /**
     * Выбор продукта
     *
     * @return void
     */
    private function handleStepProducts($start = false): void {

        // Массив для хранения выбранных продуктов
        // 'product_1', ...
        $selectedProducts = $this->userSelection['products']['predefined'];

        // Продукты компании
        $products = [
            'product_1' => 'Продукт Premium',
            'product_2' => 'Продукт Standard',
            'product_3' => 'Продукт Basic'
        ];

        // error_log('p1', json_encode($update));

        if (!$start && isset($this->callbackQueryData)) {
            error_log(json_encode($this->update['callback_query']));

            // finish_selection - окончение выбора, кнопка Далее
            $data = $this->update['callback_query']['data'];
            $message = $this->update['callback_query']['message'];
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];

            error_log('userSelection: ' . json_encode($this->userSelection['products']));

            // if (!empty($this->userSelection)) {
            // $selectedProducts = $this->userSelection['products']['predefined'];
            // }

            // $selectedProducts = Redis::hgetall("user:{$chatId}:selection1");

            if (strpos($data, 'product_') === 0) {

                // Обработка переключения состояния продукта
                $productId = explode('product_', $data)[1];

                if ($productId === 'other') {
                    $this->setStep($this->chatId, '');
                } else {
                    $this->userSelection['products']['selected'] = $productId;
                }


                // Redis::hmset("user:{$chatId}:selection1", $this->userSelection);
                // Redis::
            }

        } else {
            // Первоначальная отправка сообщения с кнопками
            $keyboard = $this->generateProductKeyboard();

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => "What products are you interested in? (select only one)",
                'reply_markup' => $keyboard,
            ]);
        }
    }

    private function handleStepCustomProduct($start = false): void {
        if (!$start && $this->update->getMessage()) {
            $userResponse = $this->update->getMessage()->getText();

            $this->userSelection['products']['custom'] = $userResponse;
            error_log('userResponse:' . $userResponse);

            // переходим к следующему шагу
            $this->setStep($this->chatId, 3);
            // $this->handleStepProducts(true);
        } else {
            $text = "Please write you custom product.";

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $text,
            ]);
        }
    }

    private function handleStepTargetCountries($start = false): void {
        if (!$start && $this->update->getMessage()) {
            $userResponse = $this->update->getMessage()->getText();

            $this->userSelection['products']['custom'] = $userResponse;
            error_log('userResponse:' . $userResponse);

            // переходим к следующему шагу
            $this->setStep($this->chatId, 3);
            // $this->handleStepProducts(true);
        } else {
            $text = "Please write you custom product.";

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $text,
            ]);
        }
    }

    private function handleStepCustomCountry($start = false): void {
        if (!$start && $this->update->getMessage()) {
            $userResponse = $this->update->getMessage()->getText();

            $this->userSelection['products']['custom'] = $userResponse;
            error_log('userResponse:' . $userResponse);

            // переходим к следующему шагу
            $this->setStep($this->chatId, 3);
            // $this->handleStepProducts(true);
        } else {
            $text = "Please write you custom product.";

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $text,
            ]);
        }
    }

    private function handleStepAdditionalComments($start = false): void {
        if (!$start && $this->update->getMessage()) {
            $userResponse = $this->update->getMessage()->getText();

            $this->userSelection['products']['custom'] = $userResponse;
            error_log('userResponse:' . $userResponse);

            // переходим к следующему шагу
            $this->setStep($this->chatId, 3);
            // $this->handleStepProducts(true);
        } else {
            $text = "Please write you custom product.";

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $text,
            ]);
        }
    }

    private function handleStepCompleteChat(): void {
        $text = "Thank you for your request. We will be in touch soon, typically within 48 hours.";

        $this->telegram->sendMessage([
            'chat_id' => $this->chatId,
            'text' => $text,
        ]);
    }

    /**
     * Выбор продукта
     *
     * @return void
     */
    private function handleStepProductsV2($start = false): void {

        // Массив для хранения выбранных продуктов
        // 'product_1', ...
        $selectedProducts = $this->userSelection['products']['predefined'];

        // Продукты компании
        $products = [
            'product_1' => 'Продукт Premium',
            'product_2' => 'Продукт Standard',
            'product_3' => 'Продукт Basic'
        ];

        // error_log('p1', json_encode($update));

        if (!$start && isset($this->update['callback_query'])) {
            error_log(json_encode($this->update['callback_query']));

            // finish_selection - окончение выбора, кнопка Далее
            $data = $this->update['callback_query']['data'];
            $message = $this->update['callback_query']['message'];
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];

            error_log('userSelection: ' . json_encode($this->userSelection['products']));

            // if (!empty($this->userSelection)) {
            // $selectedProducts = $this->userSelection['products']['predefined'];
            // }

            // $selectedProducts = Redis::hgetall("user:{$chatId}:selection1");

            if (strpos($data, 'toggle_') === 0) {

                // Обработка переключения состояния продукта
                $productId = substr($data, 7);

                // Обновляем список выбранных продуктов
                $key = array_search($productId, $selectedProducts);

                if ($key !== false) {
                    unset($selectedProducts[$key]); // Удаляем если уже выбран
                } else {
                    $selectedProducts[] = $productId; // Добавляем если не выбран
                }

                $this->userSelection['products']['predefined'] = $selectedProducts;
                // Redis::hmset("user:{$chatId}:selection1", $this->userSelection);
                // Redis::

                // Генерируем обновленную клавиатуру
                $newKeyboard = $this->generateProductKeyboard($products, $selectedProducts);

                // Обновляем сообщение
                $this->telegram->editMessageReplyMarkup([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'reply_markup' => $newKeyboard
                ]);

                // Отвечаем на callback (убираем часики)
                $this->telegram->answerCallbackQuery([
                    'callback_query_id' => $this->update['callback_query']['id']
                ]);

            } elseif ($data === 'finish_selection') {
                // Обработка завершения выбора
                if (empty($selectedProducts)) {
                    $responseText = "Вы не выбрали ни одного продукта.";
                } else {
                    $selectedNames = array_map(function($id) use ($products) {
                        return $products[$id];
                    }, $selectedProducts);

                    $responseText = "You are select:\n- " . implode("\n- ", $selectedNames);
                }

                // Удаляем клавиатуру
                $this->telegram->editMessageReplyMarkup([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'reply_markup' => new Keyboard()
                ]);

                // Отправляем результат
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $responseText,
                ]);

                // Здесь можно добавить дальнейшую логику обработки выбора
            }
        } else {
            // Первоначальная отправка сообщения с кнопками
            $keyboard = $this->generateProductKeyboard($products, $selectedProducts);

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => "Выберите продукты (нажмите для выбора/отмены):",
                'reply_markup' => $keyboard,
            ]);
        }
    }

    /**
     * Функция для генерации клавиатуры с учетом выбранных продуктов
     *
     * @param $products
     * @param $selectedProducts
     * @return Keyboard
     */
    private function generateProductKeyboardV2($products, $selectedProducts): Keyboard {
        $inlineKeyboard = [];

        foreach ($products as $id => $name) {
            $isSelected = in_array($id, $selectedProducts);
            $icon = $isSelected ? '✅' : '◻️';

            $inlineKeyboard[] = [
                Keyboard::inlineButton([
                    'text' => "$icon $name",
                    'callback_data' => "toggle_$id"
                ])
            ];
        }

        // Кнопка подтверждения
        $inlineKeyboard[] = [
            Keyboard::inlineButton([
                'text' => '🚀 Complete selection',
                'callback_data' => 'finish_selection',
            ]),
        ];

        return Keyboard::make([
            'inline_keyboard' => $inlineKeyboard,
        ]);
    }

    /**
     * Функция для генерации клавиатуры с учетом выбранных продуктов
     *
     * @param $products
     * @param $selectedProducts
     * @return Keyboard
     */
    private function generateProductKeyboard(): Keyboard {
        $inlineKeyboard = [];

        $inlineKeyboard[] = [
            Keyboard::inlineButton([
                'text' => "Online Casino Solution",
                'callback_data' => "product_1"
            ]),
            Keyboard::inlineButton([
                'text' => "Sports Betting Solutions",
                'callback_data' => "product_2"
            ]),
        ];

        $inlineKeyboard[] = [
            Keyboard::inlineButton([
                'text' => "Games Integration",
                'callback_data' => "product_3"
            ]),
            Keyboard::inlineButton([
                'text' => "Banking and Licensing",
                'callback_data' => "product_4"
            ]),
        ];

        $inlineKeyboard[] = [
            Keyboard::inlineButton([
                'text' => "Other (your answer)",
                'callback_data' => "product_other"
            ]),
        ];

        return Keyboard::make([
            'inline_keyboard' => $inlineKeyboard,
        ]);
    }

    /**
     *  Сontact details
     *
     * @return void
     */
    private function handleStepConcactDetails($start = false): void {
        if (!$start && $this->update->getMessage()) {
            $userResponse = $this->update->getMessage()->getText();

            $this->userSelection['contactDetails'] = $userResponse;
            error_log('userResponse:' . $userResponse);

            // переходим к следующему шагу
            $this->setStep($this->chatId, 2);
            $this->handleStepProducts(true);
        } else {
            $contactRequestText = "Please share your contact details.\n"
                . "Nickname in TG or other preferable way to connect";

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $contactRequestText,
            ]);
        }
    }

    /**
     *  Сontact details
     *
     * @return void
     */
    private function handleStepConcactDetailsV2(): void {

        if ($this->update->getMessage() && $this->update->getMessage()->getReplyToMessage()) {
            $replyToMessageId = $this->update->getMessage()->getReplyToMessage()->getMessageId();
            $userResponse = $this->update->getMessage()->getText();

            $this->userSelection['contactDetails'] = $userResponse;

            error_log('replyToMessageId:' . $replyToMessageId);
            error_log('userResponse:' . $userResponse);

            // переходим к следующему шагу
            $this->setStep($this->chatId, 2);
        } else {
            $contactRequestText = "Please share your contact details.\n\n"
                . "Nickname in TG or other preferable way to connect";

            // Создаем ForceReply с плейсхолдером
            $replyMarkup = Keyboard::forceReply([
                'input_field_placeholder' => 'Nickname in TG or other preferable way to connect',
                'selective' => true,
            ]);

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $contactRequestText,
                'reply_markup' => $replyMarkup,
            ]);


//        $keyboard = Keyboard::make([
//            'keyboard' => [
//                ['Skip this step']
//            ],
//            'resize_keyboard' => true,
//            'one_time_keyboard' => true,
//            'input_field_placeholder' => 'Enter your nickname here...'
//        ]);
//
//        $this->telegram->sendMessage([
//            'chat_id' => $this->chatId,
//            'text' => "Please enter your nickname:",
//            // 'reply_markup' => $keyboard
//        ]);
        }
    }

}
