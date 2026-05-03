<?php

namespace App\Filament\Resources\ListingPromotions\Pages;

use App\Filament\Resources\ListingPromotions\ListingPromotionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageListingPromotions extends ManageRecords
{
    protected static string $resource = ListingPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
