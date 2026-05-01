<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('listing_id')
                    ->relationship('listing', 'title')
                    ->searchable()
                    ->required(),
                Select::make('buyer_id')
                    ->relationship('buyer', 'name')
                    ->searchable()
                    ->required(),
                Select::make('seller_id')
                    ->relationship('seller', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount (centavos)')
                    ->numeric()
                    ->required(),
                Textarea::make('message')
                    ->rows(3),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'countered' => 'Countered',
                        'declined' => 'Declined',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('counter_amount')
                    ->label('Counter Amount (centavos)')
                    ->numeric(),
                Textarea::make('counter_message')
                    ->rows(2),
            ])
            ->columns(2);
    }
}
