<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('view_description')
                            ->form([
                                Forms\Components\Textarea::make('description')
                                    ->disabled()
                                    ->default(fn ($record) => $record->description)
                                    ->columnSpanFull()
                                    ->rows(5),
                                Forms\Components\Textarea::make('block_reason')
                                    ->label('Block Reason')
                                    ->disabled()
                                    ->default(fn ($record) => $record->block_reason)
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => filled($record->block_reason))
                            ])
                            ->modalHeading(fn ($record) => $record->title)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                    ]),
                Tables\Columns\IconColumn::make('is_blocked')
                    ->boolean()
                    ->label('Blocked'),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Estimated Time (Mins)')
                    ->numeric(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Completed / Updated At')
                    ->dateTime(),
            ])
            ->filters([
                // Daily, Weekly, Monthly Filters
                Tables\Filters\Filter::make('today')
                    ->label("Today's Tasks")
                    ->query(fn (Builder $query): Builder => $query->whereDate('updated_at', today())),

                Tables\Filters\Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])),

                Tables\Filters\Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('updated_at', now()->month)),
            ])
            ->headerActions([
                // Admin yahan se bhi task assign kar sakta hai agar chahe
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}