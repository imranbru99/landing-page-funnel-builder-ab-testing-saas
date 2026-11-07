<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FunnelStep extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'funnel_id', 'organization_id', 'page_id', 'name', 'slug', 'type',
        'position', 'canvas_x', 'canvas_y', 'settings',
    ];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function outgoing(): HasMany
    {
        return $this->hasMany(FunnelConnection::class, 'from_step_id');
    }
}