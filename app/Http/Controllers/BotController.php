<?php

namespace App\Http\Controllers;

use App\Jobs\LogRequestJob;
use App\Models\Apartment;
use App\Models\DialogState;
use App\Models\House;
use App\Models\RequestLog;
use App\Models\Resident;
use App\Models\Statistic;
use App\Telegram\Commands\StartCommand;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{

    private function getPermission($fallbackUsername)
    {
        $resident = Resident::where('telegram_username', '@' . $fallbackUsername)->first();
        return $resident !== null; // Возвращаем булево значение
    }

    private function logRequest($fallbackUsername, $requestType, $query)
    {
        LogRequestJob::dispatch($fallbackUsername, $requestType, $query);
    }

    private function handleApartmentNumber($telegram, $chatId, $text)
    {
        if (is_numeric($text)) {
            $apartment = Apartment::where('number', $text)->first();
            if ($apartment) {
                $residents = $apartment->residents;
                $residentList = []; // Создаем массив для жильцов

                foreach ($residents as $resident) {
                    $residentList[] = "Имя: " . $resident->name . ", Telegram: " . $resident->telegram_username . ", Телефон: " . $resident->phone_number;
                }

                $residentMessage = implode("\n", $residentList); // Объединяем всех жильцов в одно сообщение

                DialogState::where('chat_id', $chatId)->delete();
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "\nЖильцы кватиры номер $text:\n" . $residentMessage,
                ]);
                return $residentList;
            } else {
                DialogState::where('chat_id', $chatId)->delete();
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Данные о таком номере квартиры отсутствуют. Номер: " . $text,
                ]);
            }
        } else {
            DialogState::where('chat_id', $chatId)->delete();
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Номер квартиры должен быть исключительно в числовом формате!",
            ]);
        }
    }

    private function handleDangerMessage($telegram, $chatId, $text, $fallbackUsername)
    {

        if ($text !== "0") {
            DialogState::where('chat_id', $chatId)->delete();
            $resident = Resident::where('telegram_username', '@' . $fallbackUsername)->first();
            $user_apartments = $resident->apartments;
            $houses = House::whereIn('id', $user_apartments->pluck('house_id'))->get();
            $residents = Resident::where('status', 'active')
                ->where('danger_notices_agreement', true)->where('entrance', $user_apartments->first()->entrance)
                ->whereIn('id', function ($query) use ($houses) {
                    $query->select('resident_id')
                        ->from('apartment_resident_table')
                        ->whereIn('apartment_id', $houses->pluck('id'));
                })
                ->get();
            // Извлекаем только chat_id
            $chatIds = $residents->pluck('chat_id');
            foreach ($chatIds as $chat_id) {
                if ($chat_id == $chatId) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Благодарю Вас, сообщение отправлено тем жителям дома у кого включены уведомления о ЧП!",
                    ]);
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "От пользователя @$fallbackUsername Пришло сообщение о ЧП :\n $text",
                    ]);
                }
                $residentForStatistic = Resident::where('chat_id', $chat_id)->first();
                if ($residentForStatistic) {
                    $house_id = $houses->where('id', $residentForStatistic->apartments->first()->house_id)->first()->id ?? null;
                    \App\Models\Statistic::create([
                        'resident_id' => $residentForStatistic->id,
                        'house_id' => $house_id,
                        'type' => 'danger',
                        'content' => $text,
                    ]);
                }
            }
        } else {
            DialogState::where('chat_id', $chatId)->delete();
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Регистрация ЧП отменена, больше так не делайте :)",
            ]);
        }
    }

    private function handleIntercomMessage($telegram, $chatId, $text, $fallbackUsername)
    {

        if ($text !== "0") {
            DialogState::where('chat_id', $chatId)->delete();
            $resident = Resident::where('telegram_username', '@' . $fallbackUsername)->first();
            $user_apartments = $resident->apartments;
            $houses = House::whereIn('id', $user_apartments->pluck('house_id'))->get();
            $residents = Resident::where('status', 'active')
                ->where('intercom_notices_agreement', true)->where('entrance', $user_apartments->first()->entrance)
                ->whereIn('id', function ($query) use ($houses) {
                    $query->select('resident_id')
                        ->from('apartment_resident_table')
                        ->whereIn('apartment_id', $houses->pluck('id'));
                })
                ->get();
            // Извлекаем только chat_id
            $chatIds = $residents->pluck('chat_id');
            $inlineLayout = [
                [
                    Keyboard::inlineButton(['text' => 'Отказаться', 'callback_data' => 'data']),
                    Keyboard::inlineButton(['text' => 'Согласиться', 'callback_data' => 'data_from_btn2'])
                ]
            ];
            $keyboard = [
                'inline_keyboard' => $inlineLayout,
            ];
            $inline_button1 = array("text" => "Согласиться", "callback_data" => "agreement");
            $inline_button2 = array("text" => "Отказаться", "callback_data" => 'nonagreement');
            $inline_keyboard = [[$inline_button1, $inline_button2]];
            $keyboard = array("inline_keyboard" => $inline_keyboard);
            $replyMarkup = json_encode($keyboard);
            foreach ($chatIds as $chat_id) {
                if ($chat_id == $chatId) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Благодарю Вас, сообщение отправлено тем жителям дома у кого включены уведомления о домофоне! Ожидайте.",
                    ]);
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "От пользователя @$fallbackUsername Пришло сообщение с просьбой открыть дверь домофона:\n $text",
                        'reply_markup' => $replyMarkup,
                    ]);
                }
            }
            DialogState::updateOrCreate(
                ['chat_id' => $chatId],
                ['state' => 'awaiting_answer_callback'],
                ['telegram_username' => '@' . $fallbackUsername]
            );
            $residentForStatistic = Resident::where('chat_id', $chat_id)->first();
                if ($residentForStatistic) {
                    $house_id = $houses->where('id', $residentForStatistic->apartments->first()->house_id)->first()->id ?? null;
                    \App\Models\Statistic::create([
                        'resident_id' => $residentForStatistic->id,
                        'house_id' => $house_id,
                        'type' => 'intercom', 
                        'content' => $text,
                    ]);
                }
        } else {
            DialogState::where('chat_id', $chatId)->delete();
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Запрос на открытие домофона отменен, больше так не делайте :)",
            ]);
        }
    }

    public function setWebhook()
    {
        $telegram = new Api('7731831213:AAHJ5MRRkKZrfUjEcZmT_YooGI_iwNaC1lU');
        $response = $telegram->setWebhook(['url' => 'https://ns29lc-93-171-46-33.ru.tuna.am/webhook']);
        return $response;
    }

    public function handleWebhook(Request $request)
    {

        $telegram = new Api('7731831213:AAHJ5MRRkKZrfUjEcZmT_YooGI_iwNaC1lU');

        $update = $telegram->getWebhookUpdates();

        if (isset($update->my_chat_member)) {
            $chatMember = $update->my_chat_member;
            $newChatMemberStatus = $chatMember->new_chat_member->status;
            if ($newChatMemberStatus == 'kicked') {
                $kickedByUsername = $chatMember->from->username;
                $resident = Resident::where('telegram_username', '@' . $kickedByUsername)->first();
                $resident->status = 'kicked';
                $resident->update();
                Log::info('Кваттро Бот был заблокирован пользователем: ' . $kickedByUsername);
                return;
            }
        }

        try {
            $message = $update->getMessage();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();
            $fallbackUsername = $message->from->username;
            $requestType = 'Запрос QuatrroBot-у';
            $this->logRequest($fallbackUsername, $requestType, $text);
            if ($update->objectType() === 'callback_query') {
                $data = $update->getRelatedObject()->data;
                if ($data == "agreement") {
                    $state_data = DialogState::where('state', 'awaiting_answer_callback')->get();
                    $state_data = $state_data->last();
                    if ($state_data) {
                        $telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "Вы приняли запрос на открытие домофона!",
                        ]);
                    } else {
                        $telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "Запрос уже был принят",
                        ]);
                    }

                    $fallbackUsername = $update->callback_query->from->username;
                    $telegram->sendMessage([
                        'chat_id' => $state_data->chat_id,
                        'text' => "Ваш запрос принят пользователем @$fallbackUsername, можете с ним связаться!",
                    ]);
                    $state_data->delete();
                    return $update;
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Вы не приняли запрос на открытие домофона!",
                    ]);
                    return 'ok';
                }
            }



            $permission = $this->getPermission($fallbackUsername);
            if (!$message) {
                return response()->json(['status' => 'no message'], 400); // В случае отсутствия сообщения
            }
            if ($permission) {

                $dialogState = DialogState::firstOrCreate(['chat_id' => $chatId]);
                if ($dialogState->state === 'awaiting_apartment_number') {
                    $this->handleApartmentNumber($telegram, $chatId, $text);
                    return;
                }
                if ($dialogState->state === 'awaiting_danger_message') {
                    $this->handleDangerMessage($telegram, $chatId, $text, $fallbackUsername);
                    return;
                }

                if ($dialogState->state === 'awaiting_intercom_message') {
                    $this->handleIntercomMessage($telegram, $chatId, $text, $fallbackUsername);
                    return;
                }

                if ($text !== null && strpos($text, '/') === 0) {
                    Telegram::commandsHandler(true);
                    //    return 'Команда';
                } else {
                    Telegram::triggerCommand('unknown', $update);
                }
                return $update;
            } else {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Извините, у вас нет доступа!",
                ]);
                return $update;
            }
            return 'ok';
        } catch (\Exception $e) {
            if ($e->getCode() == 403) { // Код 403 означает, что бот был заблокирован
                // Обновляем статус пользователя в БД на 'kicked'
                Log::error('403 ERROR FROM TELEGRAM: ' . $e->getMessage());
            }
        }
        return $update;
    }
}
