<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('history')
                ->label('Task History')
                ->icon('heroicon-o-clipboard-document-check')
                ->visible(fn (): bool => \App\Filament\Pages\UserTaskHistory::canAccess())
                ->url(fn (): string => \App\Filament\Pages\UserTaskHistory::getUrl() . '?user=' . $this->record->id),
            Actions\DeleteAction::make(),
        ];
    }
}
