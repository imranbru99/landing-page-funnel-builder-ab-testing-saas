<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'category', 'is_core', 'platform_enabled',
        'dependencies', 'default_settings', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
            'platform_enabled' => 'boolean',
            'dependencies' => 'array',
            'default_settings' => 'array',
        ];
    }

    public function organizationModules(): HasMany
    {
        return $this->hasMany(OrganizationModule::class);
    }
}