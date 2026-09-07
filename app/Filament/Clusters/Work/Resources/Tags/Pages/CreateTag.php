<?php

namespace App\Filament\Clusters\Work\Resources\Tags\Pages;

use App\Filament\Clusters\Work\Resources\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
