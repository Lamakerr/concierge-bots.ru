<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HouseResource\Pages;
use App\Filament\Resources\HouseResource\RelationManagers;
use App\Models\House;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HouseResource extends Resource
{
    protected static ?string $model = House::class;
    protected static ?string $pluralLabel = 'Дома';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Улица')->required(),
                Forms\Components\TextInput::make('Номер дома')->integer()->required(),
                Forms\Components\TextInput::make('Корпус')->nullable()->integer(),
                Forms\Components\TextInput::make('Кол-во этажей')->integer()->required(),
                Forms\Components\TextInput::make('Кол-во подъездов')->integer()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('street')->label('Улица')->sortable(),
            TextColumn::make('number')->label('Номер')->sortable(),
            TextColumn::make('building')->label('Корпус')->sortable(),
            TextColumn::make('floors')->label('Этажи')->sortable(),
            TextColumn::make('entrances')->label('Входы')->sortable(),
            TextColumn::make('created_at')->label('Дата создания')->sortable(),
            TextColumn::make('updated_at')->label('Дата обновления')->sortable(),
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
            'index' => Pages\ListHouses::route('/'),
            'create' => Pages\CreateHouse::route('/create'),
            'edit' => Pages\EditHouse::route('/{record}/edit'),
        ];
    }
}
