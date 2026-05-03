<?php

namespace App\Filament\Resources\TransactionReceipts\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class TransactionReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('reference_number')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('monospace'),
                TextColumn::make('listing.title')
                    ->label('Listing')
                    ->limit(30),
                TextColumn::make('seller.username')
                    ->label('Seller')
                    ->sortable(),
                TextColumn::make('buyer_email')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'danger' => 'refunded',
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
