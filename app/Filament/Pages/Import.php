<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ResidentsImport;

class Import extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.import';
    protected static ?string $navigationLabel = 'Импорт жителей';
    protected static ?string $navigationGroup = 'Импорт'; // Опционально, если вы хотите использовать группы


    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('file')
                ->label('Импорт файла Excel')
                ->required()
                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                ->fileAttachment()
                ->afterUpload(function (Forms\Components\FileUpload $component, $file) {
                    // Обработка файла после загрузки
                    Excel::import(new ResidentsImport, $file);
                }),
        ]);
    }
}
