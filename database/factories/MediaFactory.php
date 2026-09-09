<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'image',
            'file_path' => ['post/'.fake()->uuid().'.jpg'],
            'video_url' => null,
            'caption' => fake()->sentence(),
            'mediable_type' => Post::class,
            'mediable_id' => Post::factory(),
        ];
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'video',
            'file_path' => null,
            'video_url' => 'https://www.youtube.com/embed/'.fake()->regexify('[A-Za-z0-9_-]{11}'),
        ]);
    }

    public function file(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'file',
            'file_path' => ['post/'.fake()->uuid().'.pdf'],
        ]);
    }
}
