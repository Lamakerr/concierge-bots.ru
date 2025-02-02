<?php

namespace App\Telegram\Commands;

use App\Models\DialogState;
use Illuminate\Support\Facades\Session;
use Telegram\Bot\Commands\Command;

class DangerCommand extends Command
{
    protected string $name = 'danger';
    protected string $pattern = '{username}';
    protected string $description = 'Увдомить о ЧП';


    public function handle()
    {
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();

        $username = $this->argument(
            'username',
            $fallbackUsername
        );

        $this->replyWithMessage(['text' => "Подробно опишите возникшую ситуацию, обязательно укажите дом, подъезд и этаж, в котором произошло проишествие.\nЕсли Вы случайно вызвали эту команду напишите цифру '0' в чат."]);
        DialogState::updateOrCreate(
            ['chat_id' => $chatId],
            ['state' => 'awaiting_danger_message']
        );
    
        return;
    }
}