<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),

                     Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->dehydrated(false)
                            ->saveRelationshipsUsing(function (User $record, $state) {
                                $roleIds = is_array($state) ? $state : [$state];
                                
                                // Eloquent relationship ke zariye roles sync karein
                                $record->roles()->sync($roleIds);

                                // Agar purana 'role' column bhi update karna hai
                                if (!empty($roleIds)) {
                                    $role = \Spatie\Permission\Models\Role::find($roleIds[0]);
                                    if ($role) {
                                        $record->updateQuietly(['role' => $role->name]);
                                    }
                                }
                            })
                            ->label('Role')
                            ->preload()
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->label('Department')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Direct Custom Permissions (Optional)')
                    ->description('Assign specific direct permissions to this user individually if needed')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                /** @var User $authUser */
                                $authUser = Auth::user();

                                // Super admin (admin role) sees ALL permissions
                                if ($authUser->hasRole('admin')) {
                                    return Permission::all()->pluck('name', 'id');
                                }

                                // Any other role (e.g. manager) can only grant
                                // permissions they already have themselves
                                return $authUser->getAllPermissions()->pluck('name', 'id');
                            })
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable(),
                    ])
                    ->hiddenOn('create')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'employee' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('performance')
                    ->label('Performance')
                    ->state(function (User $record): string {
                        return number_format($record->getPerformancePercentage(), 0) . '%';
                    })
                    ->badge()
                    ->color(function (User $record): string {
                        $perf = $record->getPerformancePercentage();
                        if ($perf >= 90) return 'success';
                        if ($perf >= 70) return 'warning';
                        return 'danger';
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filter by Role'),
            ])
            ->actions([
                Tables\Actions\Action::make('history')
                    ->label('History')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn (): bool => \App\Filament\Pages\UserTaskHistory::canAccess())
                    ->url(fn (User $record): string => \App\Filament\Pages\UserTaskHistory::getUrl() . '?user=' . $record->id),
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
            RelationManagers\TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
