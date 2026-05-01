<?php

namespace App\Filament\Resources\CategoryPricingOverrides\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryPricingOverrideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required()
                    ->label('Category'),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->required()
                    ->label('City'),
                TextInput::make('post_price')
                    ->label('Post Price (centavos)')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->helperText('Price in centavos. ₱1 = 100 centavos.'),
                TextInput::make('free_listings_unverified')
                    ->label('Free Listings (Unverified)')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Use default'),
                TextInput::make('free_listings_verified')
                    ->label('Free Listings (Verified)')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Use default'),
            ])
            ->columns(2);
    }
}
