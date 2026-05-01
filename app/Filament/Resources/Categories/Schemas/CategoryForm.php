<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->placeholder('None (top-level)'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('icon')
                    ->label('Emoji Icon')
                    ->maxLength(10),
                TextInput::make('post_price')
                    ->label('Post Price (centavos)')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('free_listings_unverified')
                    ->label('Free Listings (Unverified)')
                    ->numeric()
                    ->default(2),
                TextInput::make('free_listings_verified')
                    ->label('Free Listings (Verified)')
                    ->numeric()
                    ->default(10),
                KeyValue::make('fields')
                    ->label('Custom Fields (JSON)'),
                Toggle::make('is_active')
                    ->default(true),
            ])
            ->columns(2);
    }
}
