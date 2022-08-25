<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\NumberTrait;

class Number extends Field
{
    use NumberTrait;

    protected static string $component = 'NumberField';

    protected array $attributes = ['min', 'max', 'step'];

    protected bool $stars = false;

    public function stars(): static
    {
        $this->stars = true;

        return $this;
    }

    public function withStars(): bool
    {
        return $this->stars;
    }
}
