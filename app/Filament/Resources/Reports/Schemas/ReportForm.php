<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Models\Listing;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Report Details')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('listing')
                            ->label('Listing')
                            ->content(fn ($record) => $record?->listing?->title ?? '—'),

                        Placeholder::make('reporter')
                            ->label('Reported by')
                            ->content(fn ($record) => $record?->reporter?->name ?? '—'),

                        Placeholder::make('reason')
                            ->label('Reason')
                            ->content(fn ($record) => ucfirst($record?->reason ?? '—')),

                        Placeholder::make('description')
                            ->label('Description')
                            ->content(fn ($record) => $record?->description ?? 'No description provided.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Admin Actions')
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('status')
                            ->label('Status')
                            ->options([
                                'open' => 'Open',
                                'dismissed' => 'Dismiss (no action)',
                                'action_taken' => 'Action Taken',
                            ])
                            ->colors([
                                'open' => 'danger',
                                'dismissed' => 'gray',
                                'action_taken' => 'success',
                            ])
                            ->inline()
                            ->required(),

                        Textarea::make('admin_note')
                            ->label('Admin Note')
                            ->placeholder('Notes on what action was taken...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
