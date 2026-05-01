<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('region_id')
                    ->relationship('region', 'name')
                    ->searchable()
                    ->required(),
                Select::make('parent_id')
                    ->label('Parent City')
                    ->relationship('parent', 'name')
                    ->searchable(),
                Toggle::make('is_active')
                    ->default(true),
            ])
            ->columns(2);
    }
}
