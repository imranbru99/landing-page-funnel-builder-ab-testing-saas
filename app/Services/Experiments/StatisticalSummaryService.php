<?php

namespace App\Services\Experiments;

use App\Models\Experiment;
use App\Models\ExperimentEvent;

class StatisticalSummaryService
{
    public function summarize(Experiment $experiment): array
    {
        $goal = $experiment->goal_event ?: 'conversion';
        $rows = [];

        foreach ($experiment->variants as $variant) {
            $visitors = $experiment->assignments()->where('variant_id', $variant->id)->count();
            $conversions = ExperimentEvent::query()
                ->where('experiment_id', $experiment->id)
                ->where('variant_id', $variant->id)
                ->where('event_name', $goal)
                ->distinct('visitor_key')
                ->count('visitor_key');

            $rate = $visitors > 0 ? round($conversions / $visitors, 4) : 0.0;

            $rows[] = [
                'variant_id' => $variant->id,
                'key' => $variant->key,
                'name' => $variant->name,
                'is_control' => $variant->is_control,
                'weight' => $variant->weight,
                'visitors' => $visitors,
                'conversions' => $conversions,
                'conversion_rate' => $rate,
                'conversion_rate_pct' => round($rate * 100, 2),
            ];
        }

        $control = collect($rows)->firstWhere('is_control', true);
        foreach ($rows as &$row) {
            if ($control && ! $row['is_control'] && $control['conversion_rate'] > 0) {
                $row['lift_pct'] = round((($row['conversion_rate'] - $control['conversion_rate']) / $control['conversion_rate']) * 100, 2);
            } else {
                $row['lift_pct'] = null;
            }

            $row['significance'] = $this->approximateSignificance(
                $control['visitors'] ?? 0,
                $control['conversions'] ?? 0,
                $row['visitors'],
                $row['conversions']
            );
        }

        return [
            'experiment_id' => $experiment->id,
            'goal_event' => $goal,
            'variants' => $rows,
            'total_visitors' => collect($rows)->sum('visitors'),
            'total_conversions' => collect($rows)->sum('conversions'),
        ];
    }

    /**
     * Simple two-proportion z-test approximation for demo/reporting use.
     */
    public function approximateSignificance(int $n1, int $c1, int $n2, int $c2): array
    {
        if ($n1 < 30 || $n2 < 30) {
            return ['status' => 'insufficient_data', 'p_value' => null, 'z' => null];
        }

        $p1 = $c1 / max(1, $n1);
        $p2 = $c2 / max(1, $n2);
        $p = ($c1 + $c2) / max(1, $n1 + $n2);
        $se = sqrt($p * (1 - $p) * (1 / $n1 + 1 / $n2));

        if ($se <= 0) {
            return ['status' => 'insufficient_data', 'p_value' => null, 'z' => null];
        }

        $z = ($p2 - $p1) / $se;
        $pValue = 2 * (1 - $this->cdf(abs($z)));

        return [
            'status' => $pValue < 0.05 ? 'significant' : 'not_significant',
            'p_value' => round($pValue, 4),
            'z' => round($z, 4),
        ];
    }

    protected function cdf(float $x): float
    {
        return (1 + $this->erf($x / sqrt(2))) / 2;
    }

    protected function erf(float $x): float
    {
        $sign = $x < 0 ? -1 : 1;
        $x = abs($x);
        $a1 = 0.254829592;
        $a2 = -0.284496736;
        $a3 = 1.421413741;
        $a4 = -1.453152027;
        $a5 = 1.061405429;
        $p = 0.3275911;
        $t = 1 / (1 + $p * $x);
        $y = 1 - ((((($a5 * $t + $a4) * $t) + $a3) * $t + $a2) * $t + $a1) * $t * exp(-$x * $x);

        return $sign * $y;
    }
}
