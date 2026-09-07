<?php

namespace App\Filament\Clusters\Work\Resources\Media\Pages;

use App\Filament\Clusters\Work\Resources\Media\MediaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMedia extends ViewRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
