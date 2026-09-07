<?php

namespace App\Filament\Clusters\Work\Resources\Advertisements\Pages;

use App\Filament\Clusters\Work\Resources\Advertisements\AdvertisementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvertisement extends CreateRecord
{
    protected static string $resource = AdvertisementResource::class;
}
