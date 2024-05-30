<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Comment;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'content' => $this->faker->words(10, true),
            'user_id' => MoonshineUser::factory(),
        ];
    }
}
