<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MoonShine\Tests\Fixtures\Models\Cover;

class CoverFactory extends Factory
{
    protected $model = Cover::class;

    public function definition(): array
    {
        return [
            'image' => '',
        ];
    }
}