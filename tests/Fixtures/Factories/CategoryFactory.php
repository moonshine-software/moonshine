<?php

namespace MoonShine\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MoonShine\Tests\Fixtures\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'content' => $this->faker->words(4, true),
            'created_at' => now(),
            'public_at' => now(),
            'updated_at' => now(),
        ];
    }
}