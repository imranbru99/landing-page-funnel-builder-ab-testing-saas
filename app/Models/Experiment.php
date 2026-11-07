<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Experiment extends Model
{
    use BelongsToOrganization, HasUniversalSlug;

    protected $fillable = [
        'organization_id', 'project_id', 'page_id', 'name', 'slug', 'status',
        'goal_event', 'hypothesis', 'started_at', 'ended_at', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->uniqueScope(fn ($q) => $q->where('project_id', $this->project_id));
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function page(): BelongsTo { return $this->belongsTo(Page::class); }
    public function variants(): HasMany { return $this->hasMany(ExperimentVariant::class); }
    public function assignments(): HasMany { return $this->hasMany(ExperimentAssignment::class); }
    public function events(): HasMany { return $this->hasMany(ExperimentEvent::class); }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }
}