<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),

                // Task sirf Employee ko assign hoga
                Forms\Components\Select::make('assigned_to')
                    ->relationship(
                        name: 'assignee', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('role', 'employee')
                    )
                    ->label('Assign To (Employee)')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\DateTimePicker::make('due_date')
                    ->label('Expected Completion Date & Time')
                    ->seconds(false)
                    ->native(false)
                    ->required()
                    ->helperText('Set the deadline by which this task is expected to be completed. The task start time is recorded automatically when it is created.'),

            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To'),
                Tables\Columns\TextColumn::make('creator.name')->label('Assigned By'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Est. Time (Mins)')
                    ->numeric(),
                Tables\Columns\IconColumn::make('is_blocked')
                    ->boolean()
                    ->label('Blocked'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Started At')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Expected By')
                    ->dateTime(),
            ])
            ->filters([
                // Filters agar lagane hon
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = Auth::user();
        if ($user && $user->hasRole('employee')) {
            $query->where('assigned_to', $user->id);
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
