<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperimentVariant extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'experiment_id', 'organization_id', 'name', 'key', 'weight', 'is_control', 'content',
    ];

    protected function casts(): array
    {
        return ['is_control' => 'boolean', 'content' => 'array'];
    }

    public function experiment(): BelongsTo { return $this->belongsTo(Experiment::class); }
    public function assignments(): HasMany { return $this->hasMany(ExperimentAssignment::class, 'variant_id'); }
    public function events(): HasMany { return $this->hasMany(ExperimentEvent::class, 'variant_id'); }
}