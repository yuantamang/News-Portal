<?php

namespace App\Filament\Clusters\Work\Resources\Tags\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TagInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make('Tag Information')
                            ->description('This is where you can view the details of your tag.')
                            ->icon(Heroicon::Tag)
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('slug'),
                                TextEntry::make('context')
                                    ->markdown()
                                    ->columnSpanFull(),
                                ColorEntry::make('color'),
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
