<?php

namespace App\Filament\Resources\GangSheetResource\Pages;

use App\Filament\Resources\GangSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGangSheet extends EditRecord
{
    protected static string $resource = GangSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
