<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('listing.title')
                    ->label('Listing')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Offer')
                    ->money('PHP')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'info' => 'countered',
                        'danger' => 'declined',
                    ]),
                TextColumn::make('counter_amount')
                    ->label('Counter')
                    ->money('PHP'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'countered' => 'Countered',
                        'declined' => 'Declined',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
