<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\Enums\Color;

/** @method static static make(string $size = 'sm', string|Color $color = '', bool $fixed = false, bool $absolute = false) */
final class Spinner extends MoonShineComponent
{
    protected string $view = 'moonshine::components.spinner';

    public function __construct(
        public string $size = 'sm',
        public string|Color $color = '',
        public bool $fixed = false,
        public bool $absolute = false,
    ) {
        parent::__construct();

        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;
    }

    public function fixed(): self
    {
        $this->fixed = true;

        return $this;
    }

    public function absolute(): self
    {
        $this->absolute = true;

        return $this;
    }
}
