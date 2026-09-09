<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->unique()->words(2, true);

        return [
            'type' => ucfirst($type),
            'slug' => \Illuminate\Support\Str::slug($type),
        ];
    }
}
