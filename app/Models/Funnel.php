<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Funnel extends Model
{
    use BelongsToOrganization, HasUniversalSlug, SoftDeletes;

    protected $fillable = [
        'organization_id', 'project_id', 'name', 'slug', 'status', 'description', 'settings', 'published_at',
    ];

    protected function casts(): array
    {
        return ['settings' => 'array', 'published_at' => 'datetime'];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->uniqueScope(fn ($q) => $q->where('project_id', $this->project_id));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(FunnelStep::class)->orderBy('position');
    }

    public function connections(): HasMany
    {
        return $this->hasMany(FunnelConnection::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(FunnelVisit::class);
    }
}