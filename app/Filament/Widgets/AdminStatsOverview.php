<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        $totalEmployees = User::where('role', 'employee')->orWhereNull('role')->count();
        $tasksCompleted = Task::where('status', 'completed')->count();
        $tasksPending = Task::whereIn('status', ['pending', 'in_progress'])->count();
        $overdueTasks = Task::where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();

        $allUsers = User::all();
        $scores = $allUsers->map(fn($u) => $u->getPerformancePercentage());
        $avgPerformance = $scores->count() > 0 ? $scores->avg() : 0;

        return [
            Stat::make('Total Employees', (string) $totalEmployees)
                ->description('▲ 25% from last month')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Tasks Completed', (string) $tasksCompleted)
                ->description('▲ 50% from last week')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Tasks Pending', (string) $tasksPending)
                ->description('▲ 0% from last week')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Overdue Tasks', (string) $overdueTasks)
                ->description('▲ 0% from last week')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Avg. Performance', number_format($avgPerformance, 0) . '%')
                ->description('▲ 5% from last month')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),
        ];
    }
}