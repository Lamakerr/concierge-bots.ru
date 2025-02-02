<?php

namespace App\Telegram\Commands;

use App\Models\Resident;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = "start";
    protected string $description = 'Стартовая команда';
    protected string $pattern = '{username}';

    public function handle()
    {
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();
        $username = $this->argument(
            'username',
            $fallbackUsername
        );

        $keyboard = [
            ['/help', '/apartment'],
        ];

        $resident = Resident::where('telegram_username', '@' . $fallbackUsername)->first();


        if ($resident->chat_id === null) {
            $resident->chat_id = $chatId;
            $resident->update();
        }

        $resident->status = 'active';
        $resident->update();
        // $residentName = $resident->chat_id;
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $this->replyWithMessage([
            'text' => "Привет {$username}! Я бот консьерж ЖК Quattro, создан разработчиком @rkertini, чтобы помогать Вам и другим жителям дома! ",
            'reply_markup' => Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]),
        ]);
    }
}
