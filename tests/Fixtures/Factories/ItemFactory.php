<?php

namespace MoonShine\Tests\Fixtures\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'content' => $this->faker->words(5, true),
            'category_id' => Category::query()->inRandomOrder()->value('id'),
            'created_at' => now(),
            'public_at' => now(),
        ];
    }
}
