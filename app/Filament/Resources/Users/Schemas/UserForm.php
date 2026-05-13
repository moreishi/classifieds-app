<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('middle_name')
                    ->label('Middle Name')
                    ->maxLength(100),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('username')
                    ->label('Username')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(50)
                    ->helperText('Public handle shown on listings.'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->hiddenOn('edit')
                    ->required(),
                TextInput::make('gcash_number')
                    ->label('GCash Number')
                    ->maxLength(20),
                Toggle::make('gcash_verified_at')
                    ->label('GCash Verified')
                    ->formatStateUsing(fn($state) => !is_null($state))
                    ->afterStateHydrated(fn($component, $state) => $component->state(!is_null($state)))
                    ->mutateDehydratedStateUsing(fn($state) => $state ? now()->toDateTimeString() : null),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable(),
                TextInput::make('credit_balance')
                    ->label('Credits (centavos)')
                    ->numeric()
                    ->default(0),
                Select::make('reputation_tier')
                    ->options([
                        'newbie' => 'Newbie',
                        'regular' => 'Regular',
                        'trusted' => 'Trusted',
                        'pro' => 'Pro',
                    ])
                    ->default('newbie'),
            ])
            ->columns(2);
    }
}
