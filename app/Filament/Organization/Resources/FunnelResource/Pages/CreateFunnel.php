<?php

namespace App\Filament\Organization\Resources\FunnelResource\Pages;

use App\Filament\Organization\Resources\FunnelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFunnel extends CreateRecord
{
    protected static string $resource = FunnelResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['organization_id'] = $data['organization_id']
            ?? $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        return $data;
    }
}
