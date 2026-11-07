<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationModule extends Model
{
    use BelongsToOrganization;

    protected $fillable = ['organization_id', 'module_id', 'is_enabled', 'settings'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'settings' => 'array'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}