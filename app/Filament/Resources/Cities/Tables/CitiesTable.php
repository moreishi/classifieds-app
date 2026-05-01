<?php

namespace App\Filament\Resources\Cities\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('region.name')
                    ->sortable(),
                ToggleColumn::make('is_active'),
                TextColumn::make('listings_count')
                    ->label('Listings')
                    ->counts('listings')
                    ->sortable(),
            ])
            ->defaultSort('name');
    }
}
