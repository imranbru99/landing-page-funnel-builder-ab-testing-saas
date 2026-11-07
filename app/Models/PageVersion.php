<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVersion extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'page_id', 'organization_id', 'version', 'label', 'sections', 'styles', 'seo', 'created_by',
    ];

    protected function casts(): array
    {
        return ['sections' => 'array', 'styles' => 'array', 'seo' => 'array'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}