<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentEvent extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'experiment_id', 'variant_id', 'organization_id', 'visitor_key', 'event_name', 'value', 'meta',
    ];

    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'meta' => 'array'];
    }

    public function experiment(): BelongsTo { return $this->belongsTo(Experiment::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ExperimentVariant::class, 'variant_id'); }
}