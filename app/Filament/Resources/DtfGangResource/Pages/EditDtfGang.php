<?php

namespace App\Filament\Resources\DtfGangResource\Pages;

use App\Filament\Resources\DtfGangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDtfGang extends EditRecord
{
    protected static string $resource = DtfGangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
