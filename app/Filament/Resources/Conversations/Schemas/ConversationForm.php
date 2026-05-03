<?php

namespace App\Filament\Resources\Conversations\Schemas;

use App\Models\Message;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConversationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Conversation Details')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('listing')
                            ->label('Listing')
                            ->content(fn ($record) => $record?->listing?->title ?? '—'),

                        Placeholder::make('buyer')
                            ->label('Buyer')
                            ->content(fn ($record) => $record?->buyer?->publicName() ?? '—'),

                        Placeholder::make('seller')
                            ->label('Seller')
                            ->content(fn ($record) => $record?->seller?->publicName() ?? '—'),

                        Placeholder::make('last_message_at')
                            ->label('Last Activity')
                            ->content(fn ($record) => $record?->last_message_at?->format('M d, Y g:i A') ?? 'No messages'),
                    ]),

                Section::make('Messages')
                    ->schema([
                        Placeholder::make('messages')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->relationLoaded('messages')) {
                                    $record?->load('messages.sender');
                                }

                                $messages = $record?->messages()->with('sender')->oldest()->get() ?? collect();

                                if ($messages->isEmpty()) {
                                    return '<p class="text-gray-400 text-sm">No messages in this conversation.</p>';
                                }

                                $html = '<div class="space-y-2">';
                                foreach ($messages as $msg) {
                                    $side = $msg->sender_id === $record->buyer_id ? 'Buyer' : 'Seller';
                                    $time = $msg->created_at->format('g:i A, M d');
                                    $read = $msg->read_at ? '✓ Read' : '';
                                    $html .= '<div class="flex items-start gap-2 p-3 bg-gray-50 rounded-lg">';
                                    $html .= '<div class="font-medium text-xs text-gray-500 w-12 shrink-0">' . e($side) . '</div>';
                                    $html .= '<div class="flex-1 min-w-0">';
                                    $html .= '<p class="text-sm text-gray-900 whitespace-pre-wrap">' . e($msg->body) . '</p>';
                                    $html .= '<p class="text-xs text-gray-400 mt-0.5">' . e($time) . ' ' . e($read) . '</p>';
                                    $html .= '</div></div>';
                                }
                                $html .= '</div>';

                                return $html;
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
