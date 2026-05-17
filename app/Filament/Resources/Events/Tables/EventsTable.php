<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('user.username')
                    ->label('Organizer')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('vibe')
                    ->colors([
                        'danger' => 'Party',
                        'success' => 'Hustle',
                        'info' => 'Tech',
                        'warning' => 'Music',
                        'primary' => 'Art',
                    ])
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label('Date')
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),
                TextColumn::make('location_name')
                    ->label('Location')
                    ->limit(20),
                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('event_date');
    }
}
