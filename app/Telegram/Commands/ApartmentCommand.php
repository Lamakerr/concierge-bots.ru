<?php

namespace App\Telegram\Commands;

use App\Models\DialogState;
use Illuminate\Support\Facades\Session;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class ApartmentCommand extends Command
{
    protected string $name = 'apartment';
    protected string $pattern = '{username}';
    protected string $description = 'Поиск жильца по номеру квартиры';


    public function handle()
    {
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();

        $username = $this->argument(
            'username',
            $fallbackUsername
        );
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $this->replyWithMessage(['text' => "Введите номер квартиры?"]);
        DialogState::updateOrCreate(
            ['chat_id' => $chatId],
            ['state' => 'awaiting_apartment_number']
        );
    
        return;
    }
}
