<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TasksProgressPieChart extends ChartWidget
{
    protected static ?string $heading = 'Overall Progress';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '275px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data' => [12, 19, 3], // Dummy data
                    'backgroundColor' => [
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                    ],
                ],
            ],
            'labels' => ['Completed', 'In Progress', 'Pending'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
