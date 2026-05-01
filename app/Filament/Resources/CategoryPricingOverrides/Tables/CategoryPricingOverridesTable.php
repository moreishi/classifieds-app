<?php

namespace App\Filament\Resources\CategoryPricingOverrides\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryPricingOverridesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('post_price')
                    ->label('Post Price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('free_listings_unverified')
                    ->label('Free (Unverified)')
                    ->placeholder('Default'),
                TextColumn::make('free_listings_verified')
                    ->label('Free (Verified)')
                    ->placeholder('Default'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('category.name');
    }
}
