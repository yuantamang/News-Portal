<?php

namespace App\Filament\Widgets;

use App\Filament\Clusters\Work\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopPostsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top posts')
            ->query(
                Post::query()
                    ->where('status', 'published')
                    ->orderByDesc('view_count')
                    ->orderByDesc('click_count')
                    ->orderByDesc('published_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(55),
                TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('click_count')
                    ->label('Clicks')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                IconColumn::make('is_breaking')
                    ->label('Breaking')
                    ->boolean(),
                IconColumn::make('is_trending')
                    ->label('Trending')
                    ->boolean(),
            ])
            ->recordUrl(fn (Post $record): string => PostResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10]);
    }
}
