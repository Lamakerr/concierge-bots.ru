<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Session;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class UnkownCommand extends Command
{
    protected string $name = 'unknown';
    protected string $pattern = '{username}';
    protected string $description = 'Ответ по умолчанию';

    public function handle()
    {
        // Логика вашей команды
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;

        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();

        $username = $this->argument(
            'username',
            $fallbackUsername
        );
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $this->replyWithMessage([
            'text' => "Извините {$username}! Я не понимаю, используйте /help , чтобы увидеть список команд которые я знаю!"
        ]);
      
    }
}
