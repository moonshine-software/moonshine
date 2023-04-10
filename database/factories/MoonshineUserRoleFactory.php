<?php

namespace MoonShine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MoonShine\Models\MoonshineUserRole;

class MoonshineUserRoleFactory extends Factory
{
    protected $model = MoonshineUserRole::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
