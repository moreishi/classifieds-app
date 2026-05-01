<?php

namespace App\Filament\Resources\TransactionReceipts\Pages;

use App\Filament\Resources\TransactionReceipts\TransactionReceiptResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionReceipt extends CreateRecord
{
    protected static string $resource = TransactionReceiptResource::class;
}
