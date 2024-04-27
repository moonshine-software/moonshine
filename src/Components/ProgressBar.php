<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(float|int $value, string $size = 'sm', string $color = '', bool $radial = false) */
final class ProgressBar extends MoonShineComponent
{
    protected string $view = 'moonshine::components.progress-bar';

    public function __construct(
        public float|int $value,
        public string $size = 'sm',
        public string $color = '',
        public bool $radial = false,
    ) {
        parent::__construct();
    }

    public function radial(): self
    {
        $this->radial = true;

        return $this;
    }
}
