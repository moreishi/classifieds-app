<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Organizer')
                    ->relationship('user', 'username')
                    ->searchable(['username', 'email'])
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(4),
                DateTimePicker::make('event_date')
                    ->label('Event Date & Time')
                    ->required(),
                TextInput::make('location_name')
                    ->label('Location')
                    ->required()
                    ->maxLength(255),
                Select::make('vibe')
                    ->options([
                        'Party' => 'Party',
                        'Hustle' => 'Hustle',
                        'Art' => 'Art',
                        'Tech' => 'Tech',
                        'Music' => 'Music',
                        'Food' => 'Food',
                        'Sports' => 'Sports',
                        'Community' => 'Community',
                    ])
                    ->required(),
                TextInput::make('cover_image')
                    ->label('Cover Image URL')
                    ->url()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])
            ->columns(2);
    }
}
