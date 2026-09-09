<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(6),
            'slug' => fake()->unique()->slug(4),
            'context' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'image' => 'post/'.fake()->uuid().'.jpg',
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'view_count' => fake()->numberBetween(0, 5000),
            'click_count' => fake()->numberBetween(0, 500),
            'is_featured' => false,
            'is_breaking' => false,
            'is_trending' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'draft']);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'scheduled']);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'archived']);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => ['is_featured' => true]);
    }

    public function breaking(): static
    {
        return $this->state(fn (array $attributes): array => ['is_breaking' => true]);
    }

    public function trending(): static
    {
        return $this->state(fn (array $attributes): array => ['is_trending' => true]);
    }
}
