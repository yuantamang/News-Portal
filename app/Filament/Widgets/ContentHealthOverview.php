<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ContentHealthOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Content health';

    protected ?string $pollingInterval = '60s';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $draftPosts = Post::query()
            ->where('status', 'draft')
            ->count();

        $scheduledPosts = Post::query()
            ->where('status', 'scheduled')
            ->whereDate('published_at', '>=', today())
            ->count();

        $publishedWithoutViews = Post::query()
            ->where('status', 'published')
            ->where('view_count', 0)
            ->count();

        $trashedPosts = Post::onlyTrashed()->count();

        $emptyCategories = Category::query()
            ->doesntHave('posts')
            ->count();

        $emptyTags = Tag::query()
            ->doesntHave('posts')
            ->count();

        $expiringAds = Advertisement::query()
            ->where('status', 'active')
            ->whereDate('end_at', '>=', today())
            ->whereDate('end_at', '<=', today()->addDays(7))
            ->count();

        $expiredActiveAds = Advertisement::query()
            ->where('status', 'active')
            ->whereDate('end_at', '<', today())
            ->count();

        return [
            Stat::make('Draft posts', Number::format($draftPosts))
                ->description('Items waiting for editorial work')
                ->descriptionIcon(Heroicon::PencilSquare)
                ->color($draftPosts > 0 ? 'warning' : 'success'),

            Stat::make('Scheduled posts', Number::format($scheduledPosts))
                ->description('Upcoming published_at dates')
                ->descriptionIcon(Heroicon::Calendar)
                ->color('info'),

            Stat::make('Quiet published posts', Number::format($publishedWithoutViews))
                ->description('Published posts with zero views')
                ->descriptionIcon(Heroicon::EyeSlash)
                ->color($publishedWithoutViews > 0 ? 'warning' : 'success'),

            Stat::make('Empty taxonomy', Number::format($emptyCategories + $emptyTags))
                ->description(Number::format($emptyCategories).' categories, '.Number::format($emptyTags).' tags')
                ->descriptionIcon(Heroicon::Tag)
                ->color(($emptyCategories + $emptyTags) > 0 ? 'warning' : 'success'),

            Stat::make('Ad timing', Number::format($expiringAds))
                ->description(Number::format($expiredActiveAds).' active ads already expired')
                ->descriptionIcon(Heroicon::Clock)
                ->color($expiredActiveAds > 0 ? 'danger' : 'success'),

            Stat::make('Trashed posts', Number::format($trashedPosts))
                ->description('Soft-deleted posts still recoverable')
                ->descriptionIcon(Heroicon::Trash)
                ->color($trashedPosts > 0 ? 'gray' : 'success'),
        ];
    }
}
