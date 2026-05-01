<?php

namespace App\Filament\Resources\CategoryPricingOverrides;

use App\Filament\Resources\CategoryPricingOverrides\Pages\CreateCategoryPricingOverride;
use App\Filament\Resources\CategoryPricingOverrides\Pages\EditCategoryPricingOverride;
use App\Filament\Resources\CategoryPricingOverrides\Pages\ListCategoryPricingOverrides;
use App\Filament\Resources\CategoryPricingOverrides\Schemas\CategoryPricingOverrideForm;
use App\Filament\Resources\CategoryPricingOverrides\Tables\CategoryPricingOverridesTable;
use App\Models\CategoryPricingOverride;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryPricingOverrideResource extends Resource
{
    protected static ?string $model = CategoryPricingOverride::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationIcon(): string | BackedEnum | null
    {
        return Heroicon::OutlinedCurrencyDollar;
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryPricingOverrideForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryPricingOverridesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryPricingOverrides::route('/'),
            'create' => CreateCategoryPricingOverride::route('/create'),
            'edit' => EditCategoryPricingOverride::route('/{record}/edit'),
        ];
    }
}
