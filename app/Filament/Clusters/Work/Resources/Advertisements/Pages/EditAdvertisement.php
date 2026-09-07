<?php

namespace App\Filament\Clusters\Work\Resources\Advertisements\Pages;

use App\Filament\Clusters\Work\Resources\Advertisements\AdvertisementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAdvertisement extends EditRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
