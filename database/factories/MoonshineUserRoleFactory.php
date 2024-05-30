<?php

namespace MoonShine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MoonShine\Laravel\Models\MoonshineUserRole;

class MoonshineUserRoleFactory extends Factory
{
    protected $model = MoonshineUserRole::class;

    /**
     * Define the model's default state.
     *
     * @return array{name: string}
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
