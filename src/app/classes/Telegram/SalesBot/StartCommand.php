<?php

/**
 * Команда приветствия
 */

namespace App\Telegram\SalesBot;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Команда для приветствия';

    public function handle()
    {
        error_log('start');

        // Текст приветствия
        $welcomeText = "Welcome to the official SoftGamings Telegram Bot."
            . "Build your own iGaming solution today!\n\n"
            . "By pressing “Start”, you agree to share your personal data (such as contact information)."
            . "Let’s get started!";

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
        $this->replyWithPhoto([
            // 'chat_id' => $this->getUpdate()->getChat()->getId(),
            'photo' => InputFile::create($logoUrl),
            'caption' => $welcomeText,
            'reply_markup' => $inlineKeyboard,
        ]);
    }
}