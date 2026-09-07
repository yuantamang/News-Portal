<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostViews;
use App\Models\Tag;
use Carbon\CarbonPeriod;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Dashboard overview';

    protected ?string $pollingInterval = '60s';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $posts = Post::query()
            ->selectRaw('COUNT(*) as total_posts')
            ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_posts")
            ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_posts")
            ->selectRaw("SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled_posts")
            ->selectRaw('SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_posts')
            ->selectRaw('SUM(CASE WHEN is_breaking = 1 THEN 1 ELSE 0 END) as breaking_posts')
            ->selectRaw('SUM(CASE WHEN is_trending = 1 THEN 1 ELSE 0 END) as trending_posts')
            ->first();

        $ads = Advertisement::query()
            ->selectRaw('COUNT(*) as total_ads')
            ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_ads")
            ->selectRaw('SUM(view_count) as ad_views')
            ->selectRaw('SUM(click_count) as ad_clicks')
            ->first();

        $media = Media::query()
            ->selectRaw('COUNT(*) as total_media')
            ->selectRaw("SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as image_media")
            ->selectRaw("SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) as video_media")
            ->selectRaw("SUM(CASE WHEN type = 'file' THEN 1 ELSE 0 END) as file_media")
            ->first();

        $postViews = (int) Post::query()->sum('view_count');
        $postClicks = (int) Post::query()->sum('click_count');
        $trackedViews = (int) PostViews::query()->count();
        $trafficChart = $this->getDailyPostViewTrend();

        return [
            Stat::make('Posts', Number::abbreviate((int) $posts->total_posts))
                ->description(Number::format((int) $posts->published_posts).' published, '.Number::format((int) $posts->draft_posts).' drafts')
                ->descriptionIcon(Heroicon::DocumentText)
                ->chart($trafficChart)
                ->color('primary'),

            Stat::make('Post views', Number::abbreviate($postViews))
                ->description(Number::format($trackedViews).' tracked view records')
                ->descriptionIcon(Heroicon::Eye)
                ->chart($trafficChart)
                ->color('success'),

            Stat::make('Post clicks', Number::abbreviate($postClicks))
                ->description(Number::format((int) $posts->trending_posts).' trending, '.Number::format((int) $posts->breaking_posts).' breaking')
                ->descriptionIcon(Heroicon::CursorArrowRays)
                ->color('info'),

            Stat::make('Active ads', Number::abbreviate((int) $ads->active_ads))
                ->description(Number::abbreviate((int) $ads->ad_views).' views, '.Number::abbreviate((int) $ads->ad_clicks).' clicks')
                ->descriptionIcon(Heroicon::Megaphone)
                ->color('warning'),

            Stat::make('Media', Number::abbreviate((int) $media->total_media))
                ->description(Number::format((int) $media->image_media).' images, '.Number::format((int) $media->video_media).' videos, '.Number::format((int) $media->file_media).' files')
                ->descriptionIcon(Heroicon::Photo)
                ->color('gray'),

            Stat::make('Taxonomy', Number::format(Category::query()->count() + Tag::query()->count()))
                ->description(Number::format(Category::query()->count()).' categories, '.Number::format(Tag::query()->count()).' tags')
                ->descriptionIcon(Heroicon::Tag)
                ->color('primary'),
        ];
    }

    /**
     * @return array<int>
     */
    private function getDailyPostViewTrend(): array
    {
        $startDate = now()->subDays(13)->startOfDay();

        $viewsByDate = PostViews::query()
            ->where('viewed_at', '>=', $startDate)
            ->selectRaw('DATE(viewed_at) as viewed_on, COUNT(*) as aggregate')
            ->groupByRaw('DATE(viewed_at)')
            ->pluck('aggregate', 'viewed_on');

        return collect(CarbonPeriod::create($startDate, '1 day', now()->startOfDay()))
            ->map(fn ($date): int => (int) ($viewsByDate[$date->format('Y-m-d')] ?? 0))
            ->all();
    }
}
