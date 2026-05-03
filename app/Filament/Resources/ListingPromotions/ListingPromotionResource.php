<?php

namespace App\Filament\Resources\ListingPromotions;

use App\Filament\Resources\ListingPromotions\Pages\ManageListingPromotions;
use App\Models\ListingPromotion;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class ListingPromotionResource extends Resource
{
    protected static ?string $model = ListingPromotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Marketplace';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('listing_id')
                    ->relationship('listing', 'title')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable(['username', 'email'])
                    ->required(),
                Select::make('plan')
                    ->options([
                        '7d' => '7 Days (₱50)',
                        '14d' => '14 Days (₱80)',
                        '30d' => '30 Days (₱140)',
                    ])
                    ->required(),
                TextInput::make('amount_paid')
                    ->label('Amount Paid (centavos)')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('listing.title')
                    ->searchable(),
                TextColumn::make('user.username')
                    ->label('User')
                    ->searchable(),
                BadgeColumn::make('plan')
                    ->colors([
                        'info' => '7d',
                        'warning' => '14d',
                        'success' => '30d',
                    ]),
                TextColumn::make('amount_paid')
                    ->label('Paid')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageListingPromotions::route('/'),
        ];
    }
}
