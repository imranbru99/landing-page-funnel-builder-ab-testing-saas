<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    use BelongsToOrganization;

    protected $fillable = ['pipeline_id', 'organization_id', 'name', 'position', 'color'];

    public function pipeline(): BelongsTo { return $this->belongsTo(Pipeline::class); }
    public function contacts(): HasMany { return $this->hasMany(Contact::class); }
}