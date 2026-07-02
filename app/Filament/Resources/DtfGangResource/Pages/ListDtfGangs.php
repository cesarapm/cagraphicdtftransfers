<?php

namespace App\Filament\Resources\DtfGangResource\Pages;

use App\Filament\Resources\DtfGangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDtfGangs extends ListRecords
{
    protected static string $resource = DtfGangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
