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
    private $chatId;
    private $text;

    /**
     * Данные для хранения выбора пользователя в процессе прохождения шагов общения с ботов
     *
     * @var array
     */
    private array $userSelection = [
        'contactDetails' => '',
        'products' => [
            'predefined' => [],
            // свой вариант запрашиваемого продукта или услуги
            'custom' => '',
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
        $this->chatId = $this->message->getChat()->getId();
        $this->text = $this->message->getText();

        // $userSelection = json_decode(Redis::get("user:{$this->chatId}:selection"), true);

        $userSelection = Redis::get("user:{$this->chatId}:selection");

        error_log('User selection from cache: ' . json_encode($this->userSelection));

        if (!empty($data)) {
            $this->userSelection = json_decode($userSelection, true);
        }

        $step = $this->getStep($this->chatId);

        error_log($step);

        if ($step == 1) {
            $this->handleStepConcactDetails();
        } else if ($step == 2) {

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
    private function handleStepProducts(): void {

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

        if (isset($this->update['callback_query'])) {
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
    private function generateProductKeyboard($products, $selectedProducts): Keyboard {
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
     *  Сontact details
     *
     * @return void
     */
    private function handleStepConcactDetails(): void {

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
