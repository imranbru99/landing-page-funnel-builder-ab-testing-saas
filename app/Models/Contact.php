<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected $fillable = [
        'organization_id', 'project_id', 'pipeline_id', 'pipeline_stage_id', 'email',
        'first_name', 'last_name', 'phone', 'company', 'source', 'status',
        'custom_fields', 'last_activity_at',
    ];

    protected function casts(): array
    {
        return ['custom_fields' => 'array', 'last_activity_at' => 'datetime'];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function pipeline(): BelongsTo { return $this->belongsTo(Pipeline::class); }
    public function stage(): BelongsTo { return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id'); }
    public function submissions(): HasMany { return $this->hasMany(FormSubmission::class); }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? '')) ?: ($this->email ?? 'Unknown');
    }
}