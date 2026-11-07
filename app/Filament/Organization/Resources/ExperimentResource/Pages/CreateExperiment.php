<?php

namespace App\Filament\Organization\Resources\ExperimentResource\Pages;

use App\Filament\Organization\Resources\ExperimentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExperiment extends CreateRecord
{
    protected static string $resource = ExperimentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['organization_id'] = $data['organization_id']
            ?? $user?->current_organization_id
            ?? $user?->organizations()->value('organizations.id');

        return $data;
    }
}
