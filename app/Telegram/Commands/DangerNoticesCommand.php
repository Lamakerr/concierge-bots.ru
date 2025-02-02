<?php

namespace App\Telegram\Commands;

use App\Models\Resident;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class DangerNoticesCommand extends Command
{
    protected string $name = 'dangernotices';
    protected string $pattern = '{username}';
    protected string $description = 'Включение/выключение уведомлений о проишествиях в вашем доме';

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
        if($resident->danger_notices_agreement == true) {
            $resident->danger_notices_agreement = false;
            $resident->update();
            $this->replyWithMessage(['text' => 'Уведомления о ЧП выключены!']);
        } else {
            $resident->danger_notices_agreement = true;
            $resident->update();
            $this->replyWithMessage(['text' => 'Уведомления о ЧП включены!']);
        }
    }
}
