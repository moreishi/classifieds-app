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
                    ->relationship('user', 'username')
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount (centavos, + for credit)')
                    ->numeric()
                    ->required(),
                Select::make('type')
                    ->options([
                        'listing_fee' => 'Listing Fee',
                        'listing_bump' => 'Listing Bump',
                        'top_up' => 'Top Up (Buy Credits)',
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
