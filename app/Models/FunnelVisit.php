<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelVisit extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'funnel_id', 'funnel_step_id', 'organization_id', 'visitor_key', 'event', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(FunnelStep::class, 'funnel_step_id');
    }
}