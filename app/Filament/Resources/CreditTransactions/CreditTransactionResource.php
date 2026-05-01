<?php

namespace App\Filament\Resources\CreditTransactions;

use App\Filament\Resources\CreditTransactions\Pages\CreateCreditTransaction;
use App\Filament\Resources\CreditTransactions\Pages\EditCreditTransaction;
use App\Filament\Resources\CreditTransactions\Pages\ListCreditTransactions;
use App\Filament\Resources\CreditTransactions\Schemas\CreditTransactionForm;
use App\Filament\Resources\CreditTransactions\Tables\CreditTransactionsTable;
use App\Models\CreditTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CreditTransactionResource extends Resource
{
    protected static ?string $model = CreditTransaction::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Marketplace';
    }

    public static function getNavigationIcon(): string | BackedEnum | null
    {
        return Heroicon::OutlinedCreditCard;
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return CreditTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCreditTransactions::route('/'),
            'create' => CreateCreditTransaction::route('/create'),
            'edit' => EditCreditTransaction::route('/{record}/edit'),
        ];
    }
}
