<?php

namespace App\Filament\Organization\Resources\DomainResource\Pages;

use App\Filament\Organization\Resources\DomainResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['organization_id'] = $data['organization_id']
            ?? $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        return $data;
    }
}
