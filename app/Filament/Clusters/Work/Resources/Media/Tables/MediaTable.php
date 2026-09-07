<?php

namespace App\Filament\Clusters\Work\Resources\Media\Tables;

use App\Models\Advertisement;
use App\Models\Post;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('caption')
                    ->searchable()
                    ->limit(40),
                ImageColumn::make('file_path')
                    ->label('Upload Path')
                    ->disk('public')
                    ->stacked()
                    ->circular(),
                TextColumn::make('video_url')
                    ->searchable()
                    ->limit(40)
                    ->url(fn(?string $state): ?string => $state, shouldOpenInNewTab: true)
                    ->toggleable(),
                TextColumn::make('mediable_type')
                    ->label('Owner Type')
                    ->formatStateUsing(fn(?string $state): string => filled($state) ? class_basename($state) : '-')
                    ->badge(),
                TextColumn::make('mediable.title')
                    ->label('Owner'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'image' => 'Image',
                        'file' => 'File',
                        'video' => 'Video',
                    ]),
                SelectFilter::make('mediable_type')
                    ->label('Owner Type')
                    ->options([
                        Post::class => 'Post',
                        Advertisement::class => 'Advertisement',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
