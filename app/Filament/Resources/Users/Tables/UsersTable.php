<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('gcash_verified')
                    ->label('GCash')
                    ->getStateUsing(fn ($record) => $record->isGcashVerified() ? '✅ Verified' : '—')
                    ->colors([
                        'success' => '✅ Verified',
                    ]),
                TextColumn::make('listings_count')
                    ->label('Listings')
                    ->counts('listings')
                    ->sortable(),
                BadgeColumn::make('reputation_tier')
                    ->colors([
                        'gray' => 'newbie',
                        'blue' => 'regular',
                        'success' => 'trusted',
                        'warning' => 'pro',
                    ]),
                TextColumn::make('credit_balance')
                    ->label('Credits')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
