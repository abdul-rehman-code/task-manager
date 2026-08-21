<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Activity';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->with('assignee')
                    ->latest('updated_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Activity')
                    ->description(function (Task $record): string {
                        $name = $record->assignee?->name ?? 'User';
                        if ($record->is_blocked) {
                            return "{$name} reported blockage: {$record->blockage_reason}";
                        }
                        if ($record->status === 'completed') {
                            return "{$name} completed task";
                        }
                        return "{$name} updated status to {$record->status}";
                    })
                    ->icon(fn (Task $record): string => match (true) {
                        $record->is_blocked => 'heroicon-m-exclamation-triangle',
                        $record->status === 'completed' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-arrow-path',
                    })
                    ->iconColor(fn (Task $record): string => match (true) {
                        $record->is_blocked => 'danger',
                        $record->status === 'completed' => 'success',
                        default => 'warning',
                    })
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Time')
                    ->since()
                    ->alignEnd()
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
