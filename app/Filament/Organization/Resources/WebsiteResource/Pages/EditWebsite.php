<?php

namespace App\Filament\Organization\Resources\WebsiteResource\Pages;

use App\Filament\Organization\Resources\WebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsite extends EditRecord
{
    protected static string $resource = WebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
