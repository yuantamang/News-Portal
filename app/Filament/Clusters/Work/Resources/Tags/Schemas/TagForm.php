<?php

namespace App\Filament\Clusters\Work\Resources\Tags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpan('full')
                    ->schema([
                        Section::make('Tags')
                            ->description("This is where you create your tag's.")
                            ->icon(Heroicon::Tag)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->required()
                                    ->dehydrated()
                                    ->disabled(),
                                RichEditor::make('context')
                                    ->required()
                                    ->columnSpanFull(),
                                ColorPicker::make('color')
                                    ->required(),
                            ])
                    ])
            ]);
    }
}
