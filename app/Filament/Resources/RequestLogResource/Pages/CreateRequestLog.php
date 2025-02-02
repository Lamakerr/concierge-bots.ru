<?php

namespace App\Filament\Resources\RequestLogResource\Pages;

use App\Filament\Resources\RequestLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRequestLog extends CreateRecord
{
    protected static string $resource = RequestLogResource::class;
}
