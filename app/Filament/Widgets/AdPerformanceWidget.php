<?php

namespace App\Filament\Widgets;

use App\Filament\Clusters\Work\Resources\Advertisements\AdvertisementResource;
use App\Models\Advertisement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Number;

class AdPerformanceWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Advertisement performance')
            ->query(
                Advertisement::query()
                    ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                    ->orderBy('end_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('position')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('end_at')
                    ->label('Ends')
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
                TextColumn::make('ctr')
                    ->label('CTR')
                    ->state(fn (Advertisement $record): string => $record->view_count > 0
                        ? Number::percentage(($record->click_count / $record->view_count) * 100, precision: 2)
                        : '0%'),
            ])
            ->recordUrl(fn (Advertisement $record): string => AdvertisementResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10]);
    }
}
