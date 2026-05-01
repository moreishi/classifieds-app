<?php

namespace App\Filament\Resources\CreditTransactions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CreditTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),
                TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'success' => 'purchase',
                        'danger' => 'listing_fee',
                        'warning' => 'referral_bonus',
                        'info' => 'admin_adjustment',
                    ]),
                TextColumn::make('notes'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'purchase' => 'Purchase',
                        'listing_fee' => 'Listing Fee',
                        'referral_bonus' => 'Referral Bonus',
                        'admin_adjustment' => 'Admin Adjustment',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
