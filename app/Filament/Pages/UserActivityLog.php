<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UserActivityLog extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-rss'; // Side menu ka icon

    protected static ?string $navigationLabel = 'Workspace Feed'; // Menu ka naam
    protected static ?string $title = 'Workspace Feed';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'activities'; // URL path

    protected static string $view = 'filament.pages.user-activity-log';

    public function table(Table $table): Table
    {
        return $table
            ->query(Task::query()->with('assignee')->latest('updated_at'))
            ->columns([
                TextColumn::make('title')
                    ->label('Task')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('assignee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('activity')
                    ->label('Activity')
                    ->state(function (Task $record): string {
                        $name = $record->assignee?->name ?? 'User';

                        if ($record->is_blocked) {
                            return "{$name} reported a blockage";
                        }

                        if ($record->status === 'completed') {
                            return "{$name} completed the task";
                        }

                        return "{$name} updated status to {$record->status}";
                    })
                    ->badge()
                    ->color(fn (Task $record): string => match (true) {
                        $record->is_blocked => 'danger',
                        $record->status === 'completed' => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
