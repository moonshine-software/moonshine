<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(int $value, int $min = 1, int $max = 5) */
final class Rating extends MoonShineComponent
{
    protected string $view = 'moonshine::components.rating';

    public function __construct(
        public int $value,
        public int $min = 1,
        public int $max = 5
    ) {
        parent::__construct();
    }
}
