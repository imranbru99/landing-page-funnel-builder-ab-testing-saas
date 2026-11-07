<?php

namespace App\Filament\Organization\Widgets;

use App\Services\Analytics\AnalyticsTracker;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $orgId = $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        if (! $orgId) {
            return [
                Stat::make('Page views', '0'),
                Stat::make('Conversions', '0'),
                Stat::make('Form submissions', '0'),
            ];
        }

        $stats = app(AnalyticsTracker::class)->dashboardStats((int) $orgId);

        return [
            Stat::make('Page views', number_format($stats['page_views'])),
            Stat::make('Conversions', number_format($stats['conversions'])),
            Stat::make('Form submissions', number_format($stats['form_submissions'])),
            Stat::make('Unique visitors', number_format($stats['unique_visitors'])),
        ];
    }
}
