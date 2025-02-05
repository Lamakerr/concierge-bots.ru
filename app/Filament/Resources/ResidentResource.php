<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResidentResource\Pages;
use App\Filament\Resources\ResidentResource\RelationManagers;
use App\Models\House;
use App\Models\Resident;
use App\Models\Apartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;

class ResidentResource extends Resource
{
    protected static ?string $model = Resident::class;
    protected static ?string $pluralLabel = 'Жильцы';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function afterSave(): void
    {
        parent::afterSave();

        \Filament\Notifications\Notification::make()
            ->title('Успех')
            ->body('Жители были успешно импортированы.')
            ->success()
            ->send();
    }


    public static function form(Forms\Form $form): Forms\Form
    {

        return $form->schema([
            Forms\Components\TextInput::make('name')->label('ФИО')->required(),
            Forms\Components\TextInput::make('telegram_username')->label('Telegram')->required(),
            Forms\Components\TextInput::make('chat_id')->label('Telegram chat id')->nullable(),
            Forms\Components\TextInput::make('phone_number')->label('Номер телефона')->required(),

            Forms\Components\Select::make('resident_role_id')
                ->placeholder('Выберите роль')
                ->label('Роль')
                ->relationship('role', 'role')
                ->required(),

            Forms\Components\Select::make('intercom_notices_agreement')
                ->label('Домофон уведомления')
                ->options([
                    '1' => 'Активен',
                    '0' => 'Неактивен',
                ])
                ->placeholder('Выберите статус')
                ->required(),

            Forms\Components\Select::make('danger_notices_agreement')
                ->label('ЧП уведомления')
                ->options([
                    '1' => 'Активен',
                    '0' => 'Неактивен',
                ])
                ->placeholder('Выберите статус')
                ->required(),

            Forms\Components\Select::make('status')
                ->placeholder('Выберите статус')
                ->label('Статус')
                ->options([
                    'active' => 'Активен',
                    'inactive' => 'Неактивен',
                    'kicked' => 'Заблокирован',
                ])
                ->required(),

            // Компонент для квартир
            Forms\Components\Repeater::make('apartments')
                ->label('Квартиры')
                ->relationship('apartments')
                ->createItemButtonLabel('Добавить квартиру')
                ->schema([
                    Forms\Components\TextInput::make('number')
                        ->label('Номер квартиры')
                        ->required()
                       ->unique(
        table: 'apartments', // Исправлено название таблицы
        column: 'number',
        ignoreRecord: true,
        modifyRuleUsing: function (Unique $rule) {
            return $rule->where('house_id', fn($get) => $get('house_id'));
        }
    ),
                        Forms\Components\TextInput::make('floor')
                        ->label('Этаж')
                        ->required(),
                        Forms\Components\TextInput::make('entrance')
                        ->label('Подъезд')
                        ->required(),

                    Forms\Components\Select::make('house_id')
                        ->label('Выберите дом')
                        ->relationship('house', 'street')
                        ->getOptionLabelFromRecordUsing(fn(House $house) => $house->getFilamentName())
                        ->required()
                        ->placeholder('Выберите дом')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set) {
                            // Сбросить номер квартиры, если дом был изменен
                            $set('number', null);
                        }),


                    // Поля для создания нового дома, если оно отсутствует
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('house.street')
                            ->label('Улица')
                            ->required(),

                        Forms\Components\TextInput::make('house.number')
                            ->label('Номер дома')
                            ->required(),

                        Forms\Components\TextInput::make('house.building')
                            ->label('Корпус')
                            ->required(),
                    ])
                        ->label('Данные о доме')
                        ->hidden(fn($get) => $get('house_id') !== null), // Скрывать, если дом выбран
                ]),
                    ]);
    }
    

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('name')->label('ФИО')->sortable(),
                TextColumn::make('telegram_username')->label('Telegram')->sortable(),
                TextColumn::make('chat_id')->label('CHAT ID')->sortable(),
                TextColumn::make('phone_number')->label('Телефон')->sortable(),
                TextColumn::make('apartments.house.number')
                    ->label('Адрес')
                    ->formatStateUsing(function ($record) {
                        return $record->apartments->map(function ($apartment) {
                            return 'ул.' . $apartment->house->street . ', дом ' .
                                $apartment->house->number . ', Корпус: ' .
                                $apartment->house->building . 'Подъезд: '. $apartment->entrance. ', кв ' . $apartment->number;
                        })->implode(' | ');
                    })
                    ->sortable(),
                TextColumn::make('role.role')->label('Роль')->sortable()->toggleable(),
                TextColumn::make('intercom_notices_agreement')->label('Домофон')->sortable(),
                TextColumn::make('danger_notices_agreement')->label('ЧП')->sortable(),
                TextColumn::make('status')->label('Статус')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Статус')
                    ->options([
                        'active' => 'Активен',
                        'inactive' => 'Неактивен',
                        'kicked' => 'Заблокирован',
                    ]),
                Tables\Filters\SelectFilter::make('role.role')->label('Роль')
                    ->options([
                        'role' => 'Житель',
                    ]),
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
            'index' => Pages\ListResidents::route('/'),
            'create' => Pages\CreateResident::route('/create'),
            // 'import' => Pages\ImportResident::route('/{record}'),
            'edit' => Pages\EditResident::route('/{record}/edit'),
        ];
    }
}
