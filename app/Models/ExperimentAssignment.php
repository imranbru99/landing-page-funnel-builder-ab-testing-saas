<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentAssignment extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'experiment_id', 'variant_id', 'organization_id', 'visitor_key', 'assignment_method',
    ];

    public function experiment(): BelongsTo { return $this->belongsTo(Experiment::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ExperimentVariant::class, 'variant_id'); }
}