<?php

namespace App\Filament\Resources\RequestLogResource\Pages;

use App\Filament\Resources\RequestLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequestLogs extends ListRecords
{
    protected static string $resource = RequestLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
