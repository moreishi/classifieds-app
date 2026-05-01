<?php

namespace App\Filament\Resources\TransactionReceipts;

use App\Filament\Resources\TransactionReceipts\Pages\CreateTransactionReceipt;
use App\Filament\Resources\TransactionReceipts\Pages\EditTransactionReceipt;
use App\Filament\Resources\TransactionReceipts\Pages\ListTransactionReceipts;
use App\Filament\Resources\TransactionReceipts\Schemas\TransactionReceiptForm;
use App\Filament\Resources\TransactionReceipts\Tables\TransactionReceiptsTable;
use App\Models\TransactionReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransactionReceiptResource extends Resource
{
    protected static ?string $model = TransactionReceipt::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Marketplace';
    }

    public static function getNavigationIcon(): string | BackedEnum | null
    {
        return Heroicon::OutlinedReceiptRefund;
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionReceiptsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionReceipts::route('/'),
            'create' => CreateTransactionReceipt::route('/create'),
            'edit' => EditTransactionReceipt::route('/{record}/edit'),
        ];
    }
}
