<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('listing.title')
                    ->label('Listing')
                    ->limit(30),
                TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->sortable(),
                TextColumn::make('seller.name')
                    ->label('Seller')
                    ->sortable(),
                TextColumn::make('rating')
                    ->sortable(),
                TextColumn::make('comment')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
