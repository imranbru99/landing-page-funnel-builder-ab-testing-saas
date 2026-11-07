<?php

namespace App\Filament\Organization\Resources\PageResource\Pages;

use App\Filament\Organization\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['organization_id'] = $data['organization_id']
            ?? $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        return $data;
    }
}
