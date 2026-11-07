<?php

namespace App\Filament\Organization\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopesToOrganization
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        $orgId = $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        if ($orgId) {
            $query->where('organization_id', $orgId);
        }

        return $query;
    }
}
