<?php

namespace App\Filament\Clusters\Work\Schemas;

use Filament\Forms\Components\Repeater;

class MediaRepeater
{
    public static function make(string $uploadDirectory = 'media'): Repeater
    {
        return Repeater::make('media')
            ->relationship('media')
            ->schema(MediaFields::make($uploadDirectory))
            ->columns(2)
            ->addActionLabel('Add media')
            ->columnSpanFull()
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => static::normalizeData($data))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => static::normalizeData($data));
    }

    protected static function normalizeData(array $data): array
    {
        if (($data['type'] ?? null) === 'video') {
            $data['file_path'] = null;
        } else {
            $data['video_url'] = null;
        }

        return $data;
    }
}
