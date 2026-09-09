<?php

namespace App\View\Composers;

use App\Enums\AdvertisementPosition;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Supplies the data every public page implicitly shares, so it doesn't need
 * to be queried and passed individually from each public controller action:
 *
 * - $navCategories  Collection<Category>                       primary/mobile nav + footer "Sections"
 * - $breakingPost   Post|null                                   most recent published post where is_breaking = true
 * - $ads            array<string, Collection<Advertisement>>    keyed by AdvertisementPosition value
 * - $contacts       Collection<Contact>                         footer contact block
 */
class SharedLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'navCategories' => Category::query()->orderBy('id')->get(),
            'breakingPost' => Post::query()
                ->published()
                ->where('is_breaking', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->first(),
            'ads' => $this->activeAdsByPosition(),
            'contacts' => Contact::query()->orderBy('id')->get(),
        ]);
    }

    /**
     * Active ads (status = active, today within start_at/end_at), grouped by
     * position. Every position key is always present, even when empty, so
     * `$ads['header']` etc. is always safe to access from any view.
     *
     * @return Collection<string, Collection<int, Advertisement>>
     */
    protected function activeAdsByPosition(): Collection
    {
        $today = Carbon::today();

        $activeAdsByPosition = Advertisement::query()
            ->where('status', 'active')
            ->whereDate('start_at', '<=', $today)
            ->whereDate('end_at', '>=', $today)
            ->get()
            ->groupBy(fn (Advertisement $advertisement): string => $advertisement->position->value);

        return collect(AdvertisementPosition::cases())
            ->mapWithKeys(fn (AdvertisementPosition $position): array => [$position->value => collect()])
            ->merge($activeAdsByPosition);
    }
}
