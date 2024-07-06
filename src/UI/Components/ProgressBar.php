<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\Enums\Color;

/** @method static static make(float|int $value, string $size = 'sm', string|Color $color = '', bool $radial = false) */
final class ProgressBar extends MoonShineComponent
{
    protected string $view = 'moonshine::components.progress-bar';

    public function __construct(
        public float|int $value,
        public string $size = 'sm',
        public string|Color $color = '',
        public bool $radial = false,
    ) {
        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;

        parent::__construct();
    }

    public function radial(): self
    {
        $this->radial = true;

        return $this;
    }
}
