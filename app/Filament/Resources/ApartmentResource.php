<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Filament\Resources\ApartmentResource\RelationManagers;
use App\Models\Apartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApartmentResource extends Resource
{
    protected static ?string $model = Apartment::class;
    protected static ?string $pluralLabel = 'Квартиры';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')->label('Номер квартиры')->required(),
                Forms\Components\TextInput::make('floor')->label('Этаж')->integer()->required(),
                Forms\Components\TextInput::make('house.street')->label('Улица')->string(),
                Forms\Components\TextInput::make('house.number')->label('Номер дома')->integer(),
                Forms\Components\TextInput::make('house.building')->label('Корпус')->nullable()->string(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('number')->label('Номер квартиры')->sortable(),
                TextColumn::make('floor')->label('Этаж')->sortable(),
                TextColumn::make('house.street')->label('Улица')->sortable(), // Поле улицы
                TextColumn::make('house.number')->label('Номер дома')->sortable(), // Поле номера дома
                TextColumn::make('house.building')->label('Корпус')->sortable(), // Поле строения
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListApartments::route('/'),
            'create' => Pages\CreateApartment::route('/create'),
            'view' => Pages\ViewApartment::route('/{record}'),
            'edit' => Pages\EditApartment::route('/{record}/edit'),
        ];
    }
}
