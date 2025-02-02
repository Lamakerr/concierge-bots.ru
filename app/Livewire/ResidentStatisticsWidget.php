<?php

namespace App\Livewire;

use App\Models\Resident;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends;

class ResidentStatisticsWidget extends ChartWidget
{
    protected static ?string $heading = 'Пользователи бота';

    protected function getData(): array
    {
        $data = [
            'all' => Trend::model(Resident::class)->between(start: now()->startOfMonth(), end: now()->endOfMonth())->perDay()->count(),
            'active' => Trend::query(Resident::where('status', 'active'))->between(start: now()->startOfMonth(), end: now()->endOfMonth())->perDay()->count(),
            'notificationAgreements' => Trend::query(Resident::select(DB::raw('sum(intercom_notices_agreement) as intercom_agreed,sum(danger_notices_agreement) as danger_agreed')))
            ->between(start: now()->startOfMonth(), end: now()->endOfMonth())->perDay()->count(),
        ];

        // dd($data['notificationAgreements']);
        return [
            'datasets' => [
                [
                    'label' => 'Пользователи',
                    'data' => $data['all']->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Цвет для всех пользователей
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Активные пользователи',
                    'data' => $data['active']->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Цвет для активных пользователей
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Получают уведомления',
                    'data' => $data['notificationAgreements']->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(9, 214, 241, 0.2)',
                    'borderColor' => 'rgba(195, 235, 17, 0.94)',
                    'borderWidth' => 1
                ],
            ],
            'labels' => $data['all']->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
