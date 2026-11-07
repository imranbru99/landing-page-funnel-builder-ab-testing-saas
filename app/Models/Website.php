<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Website extends Model
{
    use BelongsToOrganization, HasUniversalSlug, SoftDeletes;

    protected $fillable = [
        'organization_id', 'project_id', 'name', 'slug', 'status', 'theme', 'navigation', 'seo',
    ];

    protected function casts(): array
    {
        return ['theme' => 'array', 'navigation' => 'array', 'seo' => 'array'];
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

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}