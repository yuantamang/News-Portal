<?php

namespace App\Filament\Clusters\Work\Resources\Advertisements\Schemas;

use App\Filament\Clusters\Work\Schemas\MediaRepeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpan('full')
                    ->schema([
                        Section::make("Advertisement Details")
                            ->description("Provide the details of the advertisement.")
                            ->icon(Heroicon::Megaphone)
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
                                FileUpload::make('image')
                                    ->image()
                                    ->directory('advertisement')
                                    ->disk('public')
                                    ->required(),
                                TextInput::make('link')
                                    ->required()
                                    ->url()
                                    ->columnSpanFull(),
                            ])->collapsible()->columns(2)
                    ]),

                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make("Scheduling")
                            ->description("Set the schedule for the advertisement.")
                            ->icon(Heroicon::Calendar)
                            ->schema([
                                Select::make('position')
                                    ->options([
                                        'header' => 'Header',
                                        'sidebar' => 'Sidebar',
                                        'footer' => 'Footer',
                                        'inline' => 'Inline',
                                        'popup' => 'Popup',
                                    ])
                                    ->default('popup')
                                    ->required(),
                                Select::make('status')
                                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                                    ->required(),
                                MediaRepeater::make('advertisement/media'),
                                DatePicker::make('start_at')
                                    ->required(),
                                DatePicker::make('end_at')
                                    ->required(),
                                TextInput::make('view_count')
                                    // ->required()
                                    ->numeric()
                                    ->disabled(),
                                TextInput::make('click_count')
                                    // ->required()
                                    ->numeric()
                                    ->disabled(),
                            ])->collapsible()->columns(2)
                    ])
            ]);
    }
}
