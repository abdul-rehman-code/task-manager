<?php

// namespace App\Filament\Widgets;

// use App\Models\UserActivity;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Filament\Widgets\TableWidget as BaseWidget;
// use Illuminate\Database\Eloquent\Builder;

// class WorkspaceFeedWidget extends BaseWidget
// {
//     protected static ?string $heading = 'Workspace Feed (Recent Activities)';
//     protected static ?int $sort = 6;
//     protected int | string | array $columnSpan = 'full';

//     public function table(Table $table): Table
//     {
//         return $table
//             ->query(
//                 UserActivity::query()->with('user')->latest()
//             )
//             ->columns([
//                 Tables\Columns\TextColumn::make('user.name')
//                     ->label('Employee')
//                     ->searchable()
//                     ->weight('bold'),
//                 Tables\Columns\TextColumn::make('action')
//                     ->label('Action')
//                     ->badge()
//                     ->color(fn (string $state): string => match ($state) {
//                         'created task' => 'success',
//                         'updated status' => 'warning',
//                         'started' => 'primary',
//                         'stopped' => 'gray',
//                         default => 'gray',
//                     }),
//                 Tables\Columns\TextColumn::make('description')
//                     ->label('Details')
//                     ->wrap(),
//                 Tables\Columns\TextColumn::make('created_at')
//                     ->label('Time')
//                     ->dateTime()
//                     ->sortable()
//                     ->description(fn ($record) => $record->created_at->diffForHumans()),
//             ])
//             ->paginated([5, 10])
//             ->defaultPaginationPageOption(5);
//     }
// }
