<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'project_id', 'page_id', 'funnel_id', 'visitor_key', 'session_key',
        'event_name', 'url', 'referrer', 'utm_source', 'utm_medium', 'utm_campaign',
        'properties', 'occurred_at',
    ];

    protected function casts(): array
    {
        return ['properties' => 'array', 'occurred_at' => 'datetime'];
    }

    public function page(): BelongsTo { return $this->belongsTo(Page::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function funnel(): BelongsTo { return $this->belongsTo(Funnel::class); }
}