<?php

namespace App\Filament\Clusters\Work\Resources\Media\Schemas;

use App\Filament\Clusters\Work\Schemas\MediaFields;
use App\Models\Advertisement;
use App\Models\Post;
use Filament\Forms\Components\MorphToSelect;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make('Media')
                            ->description('Select the media type and the corresponding record.')
                            ->icon(Heroicon::Photo)
                            ->schema([
                                MorphToSelect::make('mediable')
                                    ->label('Owner')
                                    ->typeSelectToggleButtons()
                                    ->types([
                                        MorphToSelect\Type::make(Post::class)
                                            ->label('Post')
                                            ->titleAttribute('title')
                                            ->searchColumns(['title', 'slug']),
                                        MorphToSelect\Type::make(Advertisement::class)
                                            ->label('Advertisement')
                                            ->titleAttribute('title')
                                            ->searchColumns(['title', 'slug']),
                                    ])
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                ...MediaFields::make('media'),
                            ])->collapsible()->columns(2)
                    ])
            ]);
    }
}
