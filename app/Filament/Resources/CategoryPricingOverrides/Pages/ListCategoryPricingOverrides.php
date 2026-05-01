<?php

namespace App\Filament\Resources\CategoryPricingOverrides\Pages;

use App\Filament\Resources\CategoryPricingOverrides\CategoryPricingOverrideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryPricingOverrides extends ListRecords
{
    protected static string $resource = CategoryPricingOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
