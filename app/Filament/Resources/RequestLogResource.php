<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestLogResource\Pages;
use App\Filament\Resources\RequestLogResource\RelationManagers;
use App\Models\RequestLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestLogResource extends Resource
{
    protected static ?string $model = RequestLog::class;
    protected static ?string $slug = 'request-logs'; // URL-ссылка ресурса
    protected static ?string $pluralLabel = 'Запросы ботам';
    protected static ?string $label = 'Лог запросов';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_name')->label('Имя пользователя'),
                Tables\Columns\TextColumn::make('request_type')->label('Тип запроса'),
                Tables\Columns\TextColumn::make('query')->label('Содержание'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Время создания'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequestLogs::route('/'),
            'create' => Pages\CreateRequestLog::route('/create'),
            'edit' => Pages\EditRequestLog::route('/{record}/edit'),
        ];
    }
}
