<?php

namespace App\Filament\Resources\TransactionReceipts\Pages;

use App\Filament\Resources\TransactionReceipts\TransactionReceiptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransactionReceipt extends EditRecord
{
    protected static string $resource = TransactionReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
