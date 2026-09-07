<?php

namespace App\Filament\Clusters\Work\Resources\Advertisements\Pages;

use App\Filament\Clusters\Work\Resources\Advertisements\AdvertisementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdvertisement extends ViewRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
