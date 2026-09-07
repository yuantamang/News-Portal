<?php

namespace App\Filament\Clusters\Work\Resources\Posts\Schemas;

use App\Filament\Clusters\Work\Schemas\MediaRepeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make()->columnSpan('full')
                    ->steps([
                        Step::make('Content')
                            ->description('Add the content of the post.')
                            ->icon(Heroicon::DocumentText)
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
                                FileUpload::make('image')
                                    ->image()
                                    ->directory('post')
                                    ->disk('public')
                                    ->required(),
                                // ->maxSize(20480),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'scheduled' => 'Scheduled',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                DatePicker::make('published_at')
                                    ->required(),
                                TextInput::make('view_count')
                                    // ->required()
                                    ->disabled()
                                    ->numeric(),
                                TextInput::make('click_count')
                                    // ->required()
                                    ->disabled()
                                    ->numeric(),
                                Toggle::make('is_featured')
                                    ->required(),
                                Toggle::make('is_breaking')
                                    ->required(),
                                Toggle::make('is_trending')
                                    ->required(),
                            ])->columns(3),

                        Step::make('Relation')
                            ->description("Put relation on them.")
                            ->icon(Heroicon::DocumentCheck)
                            ->schema([
                                Select::make('categories')
                                    ->multiple()
                                    ->preload()
                                    ->required()
                                    ->searchable()
                                    ->relationship('categories', 'type'),

                                Select::make('tags')
                                    ->multiple()
                                    ->preload()
                                    ->required()
                                    ->searchable()
                                    ->relationship('tags', 'title'),

                                MediaRepeater::make('post/media'),
                            ])
                    ])
            ]);
    }
}
