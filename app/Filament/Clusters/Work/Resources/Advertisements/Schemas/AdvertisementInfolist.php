<?php

namespace App\Filament\Clusters\Work\Resources\Advertisements\Schemas;

use App\Models\Media;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AdvertisementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make("Advertisement Details")
                            ->description("Details of the advertisement.")
                            ->icon(Heroicon::Megaphone)
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('slug'),
                                ImageEntry::make('image')
                                    ->disk('public')
                                    ->circular(),
                                TextEntry::make('link')
                                    ->columnSpanFull(),
                                TextEntry::make('position')
                                    ->badge(),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('start_at')
                                    ->date(),
                                TextEntry::make('end_at')
                                    ->date(),
                            ])->collapsible()->columns(2)
                    ]),

                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make("Details")
                            ->description("Additional details of the advertisement.")
                            ->icon(Heroicon::InformationCircle)
                            ->schema([
                                RepeatableEntry::make('media')
                                    ->columnSpanFull()
                                    ->schema([
                                        TextEntry::make('type')
                                            ->badge(),

                                        ImageEntry::make('file_path')
                                            ->disk('public')
                                            ->circular()
                                            ->stacked()
                                            ->visible(fn(Media $record): bool => $record->isImage() && filled($record->file_path)),

                                        TextEntry::make('file_path')
                                            ->label('File')
                                            ->visible(fn(Media $record): bool => $record->isFile() && filled($record->file_path)),

                                        TextEntry::make('video_url')
                                            ->label('Video URL')
                                            ->url(fn(?string $state): ?string => $state)
                                            ->openUrlInNewTab()
                                            ->visible(fn(Media $record): bool => $record->isVideo() && filled($record->video_url)),

                                        TextEntry::make('caption')
                                            ->placeholder('-'),
                                    ])
                                    ->grid(2),
                                TextEntry::make('view_count')
                                    ->numeric(),
                                TextEntry::make('click_count')
                                    ->numeric(),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ])->collapsible()->columns(2)
                    ])
            ]);
    }
}
