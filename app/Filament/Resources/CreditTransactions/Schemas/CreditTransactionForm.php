<?php

namespace App\Filament\Resources\CreditTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CreditTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount (centavos, + for credit)')
                    ->numeric()
                    ->required(),
                Select::make('type')
                    ->options([
                        'purchase' => 'Purchase',
                        'listing_fee' => 'Listing Fee',
                        'referral_bonus' => 'Referral Bonus',
                        'admin_adjustment' => 'Admin Adjustment',
                    ])
                    ->required(),
                TextInput::make('notes')
                    ->maxLength(255),
            ])
            ->columns(2);
    }
}
