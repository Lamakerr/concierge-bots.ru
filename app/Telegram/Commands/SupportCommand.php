<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class SupportCommand extends Command
{
    protected string $name = 'support';

    protected string $description = 'Поддержать разработчика :)';

    public function handle()
    {
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();
        $username = $this->argument(
            'username',
            $fallbackUsername
        );
        $this->replyWithMessage(['text' => "Большое спасибо за вашу признательность и поддержку! Вот реквизиты разработчика:\n +79519921348 - СБП/CБЕР (Керимов Руслан А.)\nЕсли Вас не затруднит, укажите в комментариях перевода - 'Поддержка разработчика' "]);
    }
}
 