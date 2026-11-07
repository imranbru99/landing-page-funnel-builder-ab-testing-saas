<?php

namespace App\Filament\Organization\Resources\WebsiteResource\Pages;

use App\Filament\Organization\Resources\WebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsites extends ListRecords
{
    protected static string $resource = WebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
