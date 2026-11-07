<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Page extends Model
{
    use BelongsToOrganization, HasUniversalSlug, SoftDeletes;

    protected $fillable = [
        'organization_id', 'project_id', 'website_id', 'template_id', 'title', 'slug',
        'status', 'publish_path', 'subdomain', 'domain_id', 'sections', 'styles', 'seo',
        'version', 'published_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'styles' => 'array',
            'seo' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->uniqueScope(fn ($q) => $q->where('project_id', $this->project_id));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PageTemplate::class, 'template_id');
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }

    public function publicUrl(): ?string
    {
        if ($this->subdomain) {
            return url('/p/'.$this->subdomain);
        }

        if ($this->publish_path) {
            return url('/sites/'.$this->publish_path);
        }

        return null;
    }
}