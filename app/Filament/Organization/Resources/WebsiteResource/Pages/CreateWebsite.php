<?php

namespace App\Filament\Organization\Resources\WebsiteResource\Pages;

use App\Filament\Organization\Resources\WebsiteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsite extends CreateRecord
{
    protected static string $resource = WebsiteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['organization_id'] = $data['organization_id']
            ?? $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        return $data;
    }
}
