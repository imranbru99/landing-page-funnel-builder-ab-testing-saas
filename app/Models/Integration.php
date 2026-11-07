<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'provider', 'name', 'status', 'credentials', 'settings', 'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'settings' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }
}