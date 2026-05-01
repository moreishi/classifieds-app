<?php

namespace App\Filament\Resources\ListingViewLogs;

use App\Filament\Resources\ListingViewLogs\Pages\CreateListingViewLog;
use App\Filament\Resources\ListingViewLogs\Pages\EditListingViewLog;
use App\Filament\Resources\ListingViewLogs\Pages\ListListingViewLogs;
use App\Filament\Resources\ListingViewLogs\Schemas\ListingViewLogForm;
use App\Filament\Resources\ListingViewLogs\Tables\ListingViewLogsTable;
use App\Models\ListingViewLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ListingViewLogResource extends Resource
{
    protected static ?string $model = ListingViewLog::class;

    public static function form(Schema $schema): Schema
    {
        return ListingViewLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ListingViewLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListListingViewLogs::route('/'),
            'create' => CreateListingViewLog::route('/create'),
            'edit' => EditListingViewLog::route('/{record}/edit'),
        ];
    }
}
