<?php

namespace App\Telegram\Commands;

use App\Models\Resident;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class IntercomNoticesCommand extends Command
{
    protected string $name = 'intercomnotices';
    protected string $pattern = '{username}';
    protected string $description = 'Включение/выключение уведомлений о запросах на открытие девери домофона';

    public function handle()
    {
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();
        $username = $this->argument(
            'username',
            $fallbackUsername
        );
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $resident = Resident::where('telegram_username', '@' . $fallbackUsername)->first();
        if($resident->intercom_notices_agreement == true) {
            $resident->intercom_notices_agreement = false;
            $resident->update();
            $this->replyWithMessage(['text' => 'Уведомления о запросах на открытие домофона выключены!']);
        } else {
            $resident->intercom_notices_agreement = true;
            $resident->update();
            $this->replyWithMessage(['text' => 'Уведомления о запросах на открытие домофона включены!']);
        }
    }
}
