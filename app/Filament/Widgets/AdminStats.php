<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description(ucfirst(User::whereDate('created_at', today())->count()) . ' joined today')
                ->icon('heroicon-o-users'),

            Stat::make('Active Listings', Listing::where('status', 'active')->count())
                ->description('Sold: ' . Listing::where('status', 'sold')->count())
                ->icon('heroicon-o-document-text'),

            Stat::make('Open Reports', Report::where('status', 'open')->count())
                ->description('Awaiting moderation')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('GCash Verifications', User::whereNotNull('gcash_verified_at')->count())
                ->description(number_format(User::whereNotNull('gcash_verified_at')->count() / max(User::count(), 1) * 100, 1) . '% of users')
                ->icon('heroicon-o-shield-check')
                ->color('success'),
        ];
    }
}
