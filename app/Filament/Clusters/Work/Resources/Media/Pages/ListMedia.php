<?php

namespace App\Filament\Clusters\Work\Resources\Media\Pages;

use App\Filament\Clusters\Work\Resources\Media\MediaResource;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
