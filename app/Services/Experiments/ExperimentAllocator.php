<?php

namespace App\Services\Experiments;

use App\Models\Experiment;
use App\Models\ExperimentAssignment;
use App\Models\ExperimentVariant;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ExperimentAllocator
{
    public const COOKIE_PREFIX = 'lf_exp_';

    public function visitorKey(?string $existing = null): string
    {
        if ($existing) {
            return $existing;
        }

        $fromCookie = request()->cookie('lf_visitor');
        if (is_string($fromCookie) && $fromCookie !== '') {
            return $fromCookie;
        }

        $key = (string) Str::uuid();
        Cookie::queue(cookie('lf_visitor', $key, 60 * 24 * 365));

        return $key;
    }

    public function assign(Experiment $experiment, ?string $visitorKey = null): ExperimentVariant
    {
        if (! $experiment->isRunning()) {
            return $experiment->variants()->where('is_control', true)->firstOrFail();
        }

        $visitorKey = $this->visitorKey($visitorKey);
        $cookieName = self::COOKIE_PREFIX.$experiment->id;

        $existing = ExperimentAssignment::query()
            ->where('experiment_id', $experiment->id)
            ->where('visitor_key', $visitorKey)
            ->first();

        if ($existing) {
            Cookie::queue(cookie($cookieName, (string) $existing->variant_id, 60 * 24 * 90));

            return $existing->variant;
        }

        $fromCookie = request()->cookie($cookieName);
        if ($fromCookie) {
            $variant = $experiment->variants()->whereKey($fromCookie)->first();
            if ($variant) {
                ExperimentAssignment::query()->firstOrCreate(
                    [
                        'experiment_id' => $experiment->id,
                        'visitor_key' => $visitorKey,
                    ],
                    [
                        'variant_id' => $variant->id,
                        'organization_id' => $experiment->organization_id,
                        'assignment_method' => 'cookie',
                    ]
                );

                return $variant;
            }
        }

        $variant = $this->weightedPick($experiment->variants()->get());

        ExperimentAssignment::query()->create([
            'experiment_id' => $experiment->id,
            'variant_id' => $variant->id,
            'organization_id' => $experiment->organization_id,
            'visitor_key' => $visitorKey,
            'assignment_method' => 'weighted',
        ]);

        Cookie::queue(cookie($cookieName, (string) $variant->id, 60 * 24 * 90));

        return $variant;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ExperimentVariant>  $variants
     */
    public function weightedPick($variants): ExperimentVariant
    {
        $total = max(1, (int) $variants->sum('weight'));
        $roll = random_int(1, $total);
        $cursor = 0;

        foreach ($variants as $variant) {
            $cursor += max(0, (int) $variant->weight);
            if ($roll <= $cursor) {
                return $variant;
            }
        }

        return $variants->first();
    }
}
