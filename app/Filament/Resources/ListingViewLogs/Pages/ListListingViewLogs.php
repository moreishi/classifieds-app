<?php

namespace App\Filament\Resources\ListingViewLogs\Pages;

use App\Filament\Resources\ListingViewLogs\ListingViewLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListListingViewLogs extends ListRecords
{
    protected static string $resource = ListingViewLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
