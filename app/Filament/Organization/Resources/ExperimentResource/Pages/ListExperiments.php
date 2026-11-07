<?php

namespace App\Filament\Organization\Resources\ExperimentResource\Pages;

use App\Filament\Organization\Resources\ExperimentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExperiments extends ListRecords
{
    protected static string $resource = ExperimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
