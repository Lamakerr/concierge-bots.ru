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

        // Проверяем наличие необходимых ключей и их значений
        if (!isset($row['apartment']) || !isset($row['floor']) || !isset($row['telegram_username']) || !isset($row['phone_number']) || !isset($row['name']) || !isset($row['entrance'])) {
            return; 
        }

        $residentRoleId = ResidentRole::first()->id; // Измените, если у вас есть логика для определения роли

        // Поиск квартиры по номеру, этажу и подъезду
        $apartment = Apartment::where('number', $row['apartment'])
            ->where('floor', $row['floor'])
            ->where('entrance', $row['entrance'])
            ->first();

        // Если квартира не найдена, создаем новую
        if (!$apartment) {
            $apartment = Apartment::create([
                'number' => $row['apartment'],
                'floor' => $row['floor'],
                'entrance' => $row['entrance'],
                'house_id' => 1001, // Убедитесь, что вы указываете существующий house_id
            ]);
        }

        // Создание или получение резидента
        $resident = Resident::firstOrCreate([
            'telegram_username' => $row['telegram_username'],
            'phone_number' => $row['phone_number'],
        ], [
            'name' => $row['name'],
            'resident_role_id' => $residentRoleId,
        ]);

        // Связь между квартирой и резидентом
        // Проверяем, существует ли уже связь, чтобы избежать дублирования
        if (!$apartment->residents()->where('residents.id', $resident->id)->exists()) {
            $apartment->residents()->attach($resident->id);
        }
    }
}
