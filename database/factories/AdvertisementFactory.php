<?php

namespace Database\Factories;

use App\Enums\AdvertisementPosition;
use App\Models\Advertisement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Advertisement>
 */
class AdvertisementFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucfirst($title),
            'slug' => \Illuminate\Support\Str::slug($title),
            'image' => 'advertisement/'.fake()->uuid().'.jpg',
            'link' => fake()->url(),
            'position' => fake()->randomElement(AdvertisementPosition::cases()),
            'status' => 'active',
            'start_at' => now()->subDay()->format('Y-m-d'),
            'end_at' => now()->addWeek()->format('Y-m-d'),
            'view_count' => 0,
            'click_count' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'inactive']);
    }

    public function position(AdvertisementPosition $position): static
    {
        return $this->state(fn (array $attributes): array => ['position' => $position]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'start_at' => now()->subMonth()->format('Y-m-d'),
            'end_at' => now()->subWeek()->format('Y-m-d'),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes): array => [
            'start_at' => now()->addWeek()->format('Y-m-d'),
            'end_at' => now()->addMonth()->format('Y-m-d'),
        ]);
    }
}
