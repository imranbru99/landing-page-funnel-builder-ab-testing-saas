<?php

namespace App\Services\Funnels;

use App\Models\Funnel;
use App\Models\FunnelStep;
use App\Models\FunnelVisit;
use App\Services\Analytics\AnalyticsTracker;

class FunnelTracker
{
    public function __construct(
        protected AnalyticsTracker $analytics
    ) {}

    public function track(Funnel $funnel, ?FunnelStep $step, string $visitorKey, string $event = 'view', array $meta = []): FunnelVisit
    {
        $visit = FunnelVisit::query()->create([
            'funnel_id' => $funnel->id,
            'funnel_step_id' => $step?->id,
            'organization_id' => $funnel->organization_id,
            'visitor_key' => $visitorKey,
            'event' => $event,
            'meta' => $meta,
        ]);

        $this->analytics->track([
            'organization_id' => $funnel->organization_id,
            'project_id' => $funnel->project_id,
            'funnel_id' => $funnel->id,
            'page_id' => $step?->page_id,
            'visitor_key' => $visitorKey,
            'event_name' => 'funnel_'.$event,
            'properties' => array_merge($meta, [
                'step_id' => $step?->id,
            ]),
        ]);

        return $visit;
    }

    public function performance(Funnel $funnel): array
    {
        $steps = $funnel->steps()->withCount([
            'outgoing',
        ])->get();

        $report = [];
        foreach ($steps as $step) {
            $views = FunnelVisit::query()
                ->where('funnel_id', $funnel->id)
                ->where('funnel_step_id', $step->id)
                ->where('event', 'view')
                ->distinct('visitor_key')
                ->count('visitor_key');

            $conversions = FunnelVisit::query()
                ->where('funnel_id', $funnel->id)
                ->where('funnel_step_id', $step->id)
                ->where('event', 'convert')
                ->distinct('visitor_key')
                ->count('visitor_key');

            $report[] = [
                'step_id' => $step->id,
                'name' => $step->name,
                'type' => $step->type,
                'position' => $step->position,
                'views' => $views,
                'conversions' => $conversions,
                'conversion_rate_pct' => $views > 0 ? round(($conversions / $views) * 100, 2) : 0,
            ];
        }

        return $report;
    }
}
