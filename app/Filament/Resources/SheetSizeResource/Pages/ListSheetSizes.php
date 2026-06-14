<?php

namespace App\Filament\Resources\SheetSizeResource\Pages;

use App\Filament\Resources\SheetSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSheetSizes extends ListRecords
{
    protected static string $resource = SheetSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
