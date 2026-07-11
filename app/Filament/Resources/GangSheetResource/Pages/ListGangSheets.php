<?php

namespace App\Filament\Resources\GangSheetResource\Pages;

use App\Filament\Resources\GangSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGangSheets extends ListRecords
{
    protected static string $resource = GangSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
