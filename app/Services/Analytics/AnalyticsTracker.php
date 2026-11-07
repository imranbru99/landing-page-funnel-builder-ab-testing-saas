<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\Experiment;
use App\Models\ExperimentEvent;
use App\Jobs\ProcessAnalyticsEvent;

class AnalyticsTracker
{
    public function track(array $payload, bool $async = false): AnalyticsEvent
    {
        $payload['occurred_at'] = $payload['occurred_at'] ?? now();

        if ($async && config('queue.default') !== 'sync') {
            ProcessAnalyticsEvent::dispatch($payload);

            return new AnalyticsEvent($payload);
        }

        return AnalyticsEvent::query()->create($payload);
    }

    public function trackExperimentEvent(
        Experiment $experiment,
        int $variantId,
        string $visitorKey,
        string $eventName,
        ?float $value = null,
        array $meta = []
    ): ExperimentEvent {
        $event = ExperimentEvent::query()->create([
            'experiment_id' => $experiment->id,
            'variant_id' => $variantId,
            'organization_id' => $experiment->organization_id,
            'visitor_key' => $visitorKey,
            'event_name' => $eventName,
            'value' => $value,
            'meta' => $meta,
        ]);

        $this->track([
            'organization_id' => $experiment->organization_id,
            'project_id' => $experiment->project_id,
            'page_id' => $experiment->page_id,
            'visitor_key' => $visitorKey,
            'event_name' => 'experiment_'.$eventName,
            'properties' => [
                'experiment_id' => $experiment->id,
                'variant_id' => $variantId,
            ],
        ]);

        return $event;
    }

    public function dashboardStats(int $organizationId): array
    {
        $base = AnalyticsEvent::query()->where('organization_id', $organizationId);

        return [
            'page_views' => (clone $base)->where('event_name', 'page_view')->count(),
            'conversions' => (clone $base)->where('event_name', 'conversion')->count(),
            'form_submissions' => (clone $base)->where('event_name', 'form_submitted')->count(),
            'unique_visitors' => (clone $base)->whereNotNull('visitor_key')->distinct('visitor_key')->count('visitor_key'),
        ];
    }
}
