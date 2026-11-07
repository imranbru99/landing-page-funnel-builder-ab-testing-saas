<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'billing_interval', 'price_cents', 'currency', 'trial_days',
        'entitlements', 'module_keys', 'max_workspaces', 'max_projects', 'max_pages',
        'max_funnels', 'max_contacts', 'ai_monthly_credits', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'entitlements' => 'array',
            'module_keys' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}