<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('icon')
                    ->label('')
                    ->size('lg'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('listings_count')
                    ->label('Listings')
                    ->counts('listings')
                    ->sortable(),
                TextColumn::make('post_price')
                    ->label('Post Price')
                    ->money('PHP')
                    ->sortable(),
                ToggleColumn::make('is_active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->defaultSort('name');
    }
}
