<?php

namespace App\Filament\Clusters\Work\Resources\Media\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make('Media Details')
                            ->description('Details about the media item.')
                            ->icon(Heroicon::Photo)
                            ->schema([
                                TextEntry::make('type')
                                    ->badge(),
                                TextEntry::make('caption')
                                    ->placeholder('-'),
                                ImageEntry::make('file_path')
                                    // ->placeholder('-')
                                    ->disk('public')
                                    ->circular()
                                    ->stacked(),
                                TextEntry::make('video_url')
                                    ->placeholder('-'),
                                TextEntry::make('mediable_type')
                                    ->label('Owner Type')
                                    ->formatStateUsing(fn(?string $state): string => filled($state) ? class_basename($state) : '-'),
                                TextEntry::make('mediable.title')
                                    ->label('Owner')
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ])->columns(2)->collapsible()
                    ])
            ]);
    }
}
