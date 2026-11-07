<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'project_id', 'host', 'type', 'verification_token',
        'status', 'ssl_enabled', 'verified_at', 'dns_instructions',
    ];

    protected function casts(): array
    {
        return [
            'ssl_enabled' => 'boolean',
            'verified_at' => 'datetime',
            'dns_instructions' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}