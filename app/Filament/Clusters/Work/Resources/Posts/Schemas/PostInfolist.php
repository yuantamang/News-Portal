<?php

namespace App\Filament\Clusters\Work\Resources\Posts\Schemas;

use App\Models\Media;
use App\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make()->columnSpanFull()
                    ->steps([
                        Step::make('Content')
                            ->description("Add the content of the post.")
                            ->icon(Heroicon::DocumentText)
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('slug'),
                                TextEntry::make('context')
                                    ->columnSpanFull()
                                    ->markdown(),
                                ImageEntry::make('image')
                                    ->disk('public')
                                    ->circular(),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('published_at')
                                    ->date(),
                                TextEntry::make('view_count')
                                    ->numeric(),
                                TextEntry::make('click_count')
                                    ->numeric(),
                                IconEntry::make('is_featured')
                                    ->boolean(),
                                IconEntry::make('is_breaking')
                                    ->boolean(),
                                IconEntry::make('is_trending')
                                    ->boolean(),
                            ])->columns(3),

                        Step::make('Relation')
                            ->description("Put relation on them.")
                            ->icon(Heroicon::DocumentCheck)
                            ->schema([
                                TextEntry::make('categories.type')
                                    ->badge(),
                                TextEntry::make('tags.title')
                                    ->badge(),

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

                                TextEntry::make('deleted_at')
                                    ->dateTime()
                                    ->visible(fn(Post $record): bool => $record->trashed()),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ])->columns(3)
                    ])
            ]);
    }
}
