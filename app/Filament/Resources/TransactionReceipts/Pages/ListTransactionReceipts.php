<?php

namespace App\Filament\Resources\TransactionReceipts\Pages;

use App\Filament\Resources\TransactionReceipts\TransactionReceiptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactionReceipts extends ListRecords
{
    protected static string $resource = TransactionReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
