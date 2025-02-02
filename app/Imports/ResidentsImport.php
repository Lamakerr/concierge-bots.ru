<?php

namespace App\Imports;

use App\Models\Apartment;
use App\Models\Resident;
use App\Models\ResidentRole;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ResidentsImport implements WithHeadingRow, OnEachRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();
        // dd($row);
        // Проверяем наличие необходимых ключей и их значений
        if (!isset($row['apartment']) || !isset($row['floor']) || !isset($row['telegram_username']) || !isset($row['phone_number']) || !isset($row['name'])) {
            // Если ключ отсутствует, можно просто выйти из функции или использовать continue
            return; // Или continue; если это в цикле
        }
        $residentRoleId = ResidentRole::first()->id; // Измените, если у вас есть логика для определения роли

        // Создание или получение квартиры
        $apartment = Apartment::firstOrCreate([
            'number' => $row['apartment'], // Номер квартиры
            'floor' => $row['floor'], 
            'entrance' => 1, // Подъезд
            'house_id' => 1001, // Убедитесь, что вы указываете существующий house_id
        ]);

        // Создание или получение резидента
        $resident = Resident::firstOrCreate([
            'telegram_username' => $row['telegram_username'], // Имя пользователя Telegram
            'phone_number' => $row['phone_number'],       // Номер телефона
        ], [
            'name' => $row['name'],               // ФИО
            'resident_role_id' => $residentRoleId,
        ]);

        // Связь между квартирой и резидентом
        $apartment->residents()->attach($resident->id);
    }
}
