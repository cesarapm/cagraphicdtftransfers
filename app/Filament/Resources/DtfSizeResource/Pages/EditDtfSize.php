<?php

namespace App\Filament\Resources\DtfSizeResource\Pages;

use App\Filament\Resources\DtfSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDtfSize extends EditRecord
{
    protected static string $resource = DtfSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
