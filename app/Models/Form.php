<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Form extends Model
{
    use BelongsToOrganization, HasUniversalSlug;

    protected $fillable = [
        'organization_id', 'project_id', 'name', 'slug', 'type', 'fields', 'settings', 'is_active',
    ];

    protected function casts(): array
    {
        return ['fields' => 'array', 'settings' => 'array', 'is_active' => 'boolean'];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->uniqueScope(fn ($q) => $q->where('organization_id', $this->organization_id));
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function submissions(): HasMany { return $this->hasMany(FormSubmission::class); }
}