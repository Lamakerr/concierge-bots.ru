<?php

namespace App\Telegram\Commands;

use App\Models\DialogState;
use Telegram\Bot\Commands\Command;

class IntercomCommand extends Command
{
    protected string $name = 'intercom';
    protected string $description = 'Запрос на открытие двери домофона';
    protected string $pattern = '{username}';

    public function handle()
    {
        // Логика вашей команды
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();
        $this->replyWithMessage(['text' => 'Введите диапозон времени когда необходимо открыть дверь, так же можете описать возникшую ситуацию, чтобы жителям было более информативней']);
        DialogState::updateOrCreate(
            ['chat_id' => $chatId],
            ['state' => 'awaiting_intercom_message']
        );
        return;
    }
}
