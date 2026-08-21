<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TasksDataLineChart extends ChartWidget
{
    protected static ?string $heading = 'Tasks Data';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Tasks Completed',
                    'data' => [0, 10, 5, 2, 21, 32, 45], // Dummy data
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
