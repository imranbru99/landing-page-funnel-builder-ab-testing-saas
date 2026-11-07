<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'name', 'status', 'trigger_type', 'trigger_config', 'actions',
    ];

    protected function casts(): array
    {
        return ['trigger_config' => 'array', 'actions' => 'array'];
    }

    public function runs(): HasMany
    {
        return $this->hasMany(WorkflowRun::class);
    }
}