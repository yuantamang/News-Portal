<?php

namespace App\Filament\Widgets;

use App\Models\PostViews;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class PostViewsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Post views';

    protected ?string $description = 'Tracked views from the last 14 days.';

    protected ?string $pollingInterval = '60s';

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $startDate = now()->subDays(13)->startOfDay();

        $viewsByDate = PostViews::query()
            ->where('viewed_at', '>=', $startDate)
            ->selectRaw('DATE(viewed_at) as viewed_on, COUNT(*) as aggregate')
            ->groupByRaw('DATE(viewed_at)')
            ->pluck('aggregate', 'viewed_on');

        $period = collect(CarbonPeriod::create($startDate, '1 day', now()->startOfDay()));

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $period
                        ->map(fn ($date): int => (int) ($viewsByDate[$date->format('Y-m-d')] ?? 0))
                        ->all(),
                ],
            ],
            'labels' => $period
                ->map(fn ($date): string => $date->format('M j'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
