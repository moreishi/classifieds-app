<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('listing_id')
                    ->relationship('listing', 'title')
                    ->searchable()
                    ->required(),
                Select::make('reviewer_id')
                    ->relationship('reviewer', 'username')
                    ->searchable()
                    ->required(),
                Select::make('seller_id')
                    ->relationship('seller', 'username')
                    ->searchable()
                    ->required(),
                Select::make('transaction_receipt_id')
                    ->relationship('transactionReceipt', 'reference_number')
                    ->searchable()
                    ->required(),
                TextInput::make('rating')
                    ->label('Rating (1-5)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Textarea::make('comment')
                    ->rows(3),
            ])
            ->columns(2);
    }
}
