<?php

namespace App\Filament\Resources\Listings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Seller')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->maxLength(100)
                    ->required(),
                TextInput::make('slug')
                    ->maxLength(255)
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->rows(5),
                TextInput::make('price')
                    ->label('Price (centavos)')
                    ->numeric()
                    ->required(),
                Select::make('condition')
                    ->options([
                        'brand_new' => 'Brand New',
                        'like_new' => 'Like New',
                        'used' => 'Used',
                        'for_parts' => 'For Parts',
                    ]),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'sold' => 'Sold',
                        'expired' => 'Expired',
                    ])
                    ->default('active')
                    ->required(),
                Toggle::make('is_featured')
                    ->label('Featured'),
            ])
            ->columns(2);
    }
}
