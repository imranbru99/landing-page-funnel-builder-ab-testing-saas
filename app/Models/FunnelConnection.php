<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelConnection extends Model
{
    protected $fillable = [
        'funnel_id', 'from_step_id', 'to_step_id', 'label', 'condition_type', 'condition',
    ];

    protected function casts(): array
    {
        return ['condition' => 'array'];
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function fromStep(): BelongsTo
    {
        return $this->belongsTo(FunnelStep::class, 'from_step_id');
    }

    public function toStep(): BelongsTo
    {
        return $this->belongsTo(FunnelStep::class, 'to_step_id');
    }
}