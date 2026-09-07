<?php

namespace App\Filament\Clusters\Work\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class MediaFields
{
    /**
     * @return array<Component>
     */
    public static function make(string $uploadDirectory = 'media'): array
    {
        return [
            Select::make('type')
                ->options([
                    'image' => 'Image',
                    'file' => 'File',
                    'video' => 'Video',
                ])
                ->default('image')
                ->live()
                ->required(),
            FileUpload::make('file_path')
                ->label('Upload')
                ->multiple()
                ->disk('public')
                ->directory($uploadDirectory)
                ->helperText('Upload images or files here. Use a URL when the media type is video.')
                ->hidden(fn (Get $get): bool => $get('type') === 'video')
                ->required(fn (Get $get): bool => in_array($get('type'), ['image', 'file'], true)),
            TextInput::make('video_url')
                ->label('Video URL')
                ->url()
                ->maxLength(2048)
                ->placeholder('https://www.youtube.com/watch?v=...')
                ->hidden(fn (Get $get): bool => $get('type') !== 'video')
                ->required(fn (Get $get): bool => $get('type') === 'video'),
            TextInput::make('caption')
                ->maxLength(255)
                ->columnSpanFull(),
        ];
    }
}
