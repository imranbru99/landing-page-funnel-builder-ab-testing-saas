<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class PageTemplate extends Model
{
    use HasUniversalSlug;

    protected $fillable = [
        'name', 'slug', 'category', 'description', 'thumbnail_path', 'sections',
        'is_marketplace', 'is_premium', 'installs', 'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'is_marketplace' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'template_id');
    }
}