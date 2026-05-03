<?php

namespace App\Filament\Resources\TransactionReceipts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('listing_id')
                    ->relationship('listing', 'title')
                    ->searchable()
                    ->required(),
                Select::make('seller_id')
                    ->relationship('seller', 'username')
                    ->searchable()
                    ->required(),
                TextInput::make('buyer_email')
                    ->email()
                    ->required(),
                TextInput::make('buyer_name')
                    ->maxLength(100),
                TextInput::make('reference_number')
                    ->maxLength(20)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('amount')
                    ->label('Amount (centavos)')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'refunded' => 'Refunded',
                    ])
                    ->default('completed'),
            ])
            ->columns(2);
    }
}
