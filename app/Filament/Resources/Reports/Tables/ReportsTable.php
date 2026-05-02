<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Models\Report;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('listing.title')
                    ->label('Listing')
                    ->searchable()
                    ->limit(40)
                    ->url(fn (Report $record): string => route('listing.show', $record->listing))
                    ->openUrlInNewTab(),

                TextColumn::make('reporter.name')
                    ->label('Reported by')
                    ->searchable(),

                BadgeColumn::make('reason')
                    ->label('Reason')
                    ->colors([
                        'danger' => ['scam'],
                        'warning' => ['spam', 'misleading', 'duplicate'],
                        'info' => ['prohibited', 'wrong_category'],
                        'gray' => ['other'],
                    ]),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'open',
                        'success' => 'action_taken',
                        'gray' => 'dismissed',
                    ]),

                TextColumn::make('created_at')
                    ->label('Reported')
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),

                TextColumn::make('handled_at')
                    ->label('Handled')
                    ->dateTime('M d, Y g:i A')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'dismissed' => 'Dismissed',
                        'action_taken' => 'Action Taken',
                    ]),
                SelectFilter::make('reason')
                    ->options([
                        'spam' => 'Spam',
                        'misleading' => 'Misleading',
                        'scam' => 'Scam',
                        'prohibited' => 'Prohibited',
                        'duplicate' => 'Duplicate',
                        'wrong_category' => 'Wrong Category',
                        'other' => 'Other',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
