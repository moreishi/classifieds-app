<?php

namespace App\Filament\Resources\ListingViewLogs\Pages;

use App\Filament\Resources\ListingViewLogs\ListingViewLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditListingViewLog extends EditRecord
{
    protected static string $resource = ListingViewLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
