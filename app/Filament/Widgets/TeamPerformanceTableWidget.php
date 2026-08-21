<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TeamPerformanceTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Team Performance';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return User::query()
                    ->with('department');
            })
            ->filters([
                SelectFilter::make('time_period')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'all' => 'All Time',
                    ])
                    ->default('daily')
                    ->query(function (Builder $query, array $data) {
                        // Do nothing here; we manually use the filter state in the columns
                    })
            ])
            ->columns([
                Tables\Columns\TextColumn::make('row_index')
                    ->label('#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Employee')
                    ->description(fn (User $record): string => $record->department?->name ?? 'Team Member')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('active_task')
                    ->label('Current Task')
                    ->state(function (User $record) {
                        $task = $record->assignedTasks()->whereIn('status', ['in_progress', 'pending'])->latest()->first();
                        return $task ? $task->title : 'No active task';
                    })
                    ->limit(20)
                    ->color(fn ($state) => $state === 'No active task' ? 'gray' : 'primary'),

                Tables\Columns\TextColumn::make('tasks_total_count')
                    ->label('Total Tasks')
                    ->alignCenter()
                    ->weight('bold')
                    ->state(function (User $record, Tables\Contracts\HasTable $livewire) {
                        $period = $livewire->getTableFilterState('time_period')['value'] ?? 'daily';
                        $startDate = match($period) {
                            'daily' => now()->startOfDay(),
                            'weekly' => now()->startOfWeek(),
                            'monthly' => now()->startOfMonth(),
                            default => null,
                        };
                        $query = $record->assignedTasks();
                        if ($startDate) $query->where('created_at', '>=', $startDate);
                        return $query->count();
                    }),

                Tables\Columns\TextColumn::make('tasks_completed_count')
                    ->label('Completed')
                    ->alignCenter()
                    ->weight('bold')
                    ->color('success')
                    ->state(function (User $record, Tables\Contracts\HasTable $livewire) {
                        $period = $livewire->getTableFilterState('time_period')['value'] ?? 'daily';
                        $startDate = match($period) {
                            'daily' => now()->startOfDay(),
                            'weekly' => now()->startOfWeek(),
                            'monthly' => now()->startOfMonth(),
                            default => null,
                        };
                        $query = $record->assignedTasks()->where('status', 'completed');
                        if ($startDate) $query->where('created_at', '>=', $startDate);
                        return $query->count();
                    }),

                Tables\Columns\TextColumn::make('tasks_pending_count')
                    ->label('Pending')
                    ->alignCenter()
                    ->weight('bold')
                    ->color('warning')
                    ->state(function (User $record, Tables\Contracts\HasTable $livewire) {
                        $period = $livewire->getTableFilterState('time_period')['value'] ?? 'daily';
                        $startDate = match($period) {
                            'daily' => now()->startOfDay(),
                            'weekly' => now()->startOfWeek(),
                            'monthly' => now()->startOfMonth(),
                            default => null,
                        };
                        $query = $record->assignedTasks()->whereIn('status', ['pending', 'in_progress']);
                        if ($startDate) $query->where('created_at', '>=', $startDate);
                        return $query->count();
                    }),

                Tables\Columns\TextColumn::make('tasks_overdue_count')
                    ->label('Overdue')
                    ->alignCenter()
                    ->weight('bold')
                    ->color('danger')
                    ->state(function (User $record, Tables\Contracts\HasTable $livewire) {
                        $period = $livewire->getTableFilterState('time_period')['value'] ?? 'daily';
                        $startDate = match($period) {
                            'daily' => now()->startOfDay(),
                            'weekly' => now()->startOfWeek(),
                            'monthly' => now()->startOfMonth(),
                            default => null,
                        };
                        $query = $record->assignedTasks()
                            ->where('status', '!=', 'completed')
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                        if ($startDate) $query->where('created_at', '>=', $startDate);
                        return $query->count();
                    }),

                Tables\Columns\ViewColumn::make('performance')
                    ->label('Performance')
                    ->view('filament.tables.columns.progress-bar')
                    ->state(function (User $record, Tables\Contracts\HasTable $livewire) {
                        $period = $livewire->getTableFilterState('time_period')['value'] ?? 'daily';
                        $startDate = match($period) {
                            'daily' => now()->startOfDay(),
                            'weekly' => now()->startOfWeek(),
                            'monthly' => now()->startOfMonth(),
                            default => null,
                        };
                        return $record->getPerformancePercentage($startDate, null);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (User $record): string => url('/admin/daily-performances?tableFilters[user_id][value]=' . $record->id))
                    ->tooltip('View User Performance'),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
