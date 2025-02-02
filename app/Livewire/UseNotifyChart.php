<?php

namespace App\Livewire;

use App\Models\Statistic;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UseNotifyChart extends ChartWidget
{
    protected static ?string $heading = 'Использование уведомлений';

    protected function getData(): array
    {
            
        $data = [
            'all' => Trend::model(Statistic::class)->between(start: now()->startOfMonth(), end: now()->endOfMonth())->perDay()->count(),
            'danger' => Trend::query(Statistic::where('type', 'danger'))->between(start: now()->startOfMonth(), end: now()->endOfMonth())->perDay()->count(),
            'intercom' => Trend::query(Statistic::where('type', 'intercom'))->between(start: now()->startOfMonth(), end: now()->endOfMonth())->perDay()->count(),
           
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Уведомления о ЧП',
                    'data' => $data['danger']->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Цвет для всех пользователей
                    'borderColor' => 'rgb(247, 16, 16)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Уведомление о домофоне',
                    'data' => $data['intercom']->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Цвет для активных пользователей
                    'borderColor' => 'rgb(56, 228, 79)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Все уведомления',
                    'data' => $data['all']->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(9, 214, 241, 0.2)',
                    'borderColor' => 'rgba(17, 155, 235, 0.94)',
                    'borderWidth' => 1
                ],
            ],
            'labels' => $data['all']->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
