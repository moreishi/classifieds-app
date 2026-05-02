<?php

namespace App\Filament\Resources\Conversations\Tables;

use App\Models\Conversation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_message_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('listing.title')
                    ->label('Listing')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('listing.reference_id')
                    ->label('Ref')
                    ->searchable()
                    ->width(120),

                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable(),

                TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable(),

                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages'),

                TextColumn::make('last_message_at')
                    ->label('Last Message')
                    ->dateTime('M d, Y g:i A')
                    ->sortable()
                    ->placeholder('Never'),

                TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('listing_id')
                    ->label('Listing')
                    ->relationship('listing', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
