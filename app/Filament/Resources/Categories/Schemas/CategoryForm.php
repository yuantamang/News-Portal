<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Category Info')
                            ->description("Create Category for your use.")
                            ->icon(Heroicon::ArchiveBox)
                            ->schema([
                                TextInput::make('type')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                            ])->collapsible()
                    ])
            ]);
    }
}
