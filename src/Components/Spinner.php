<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(string $size = 'sm', string $color = '', bool $fixed = false, bool $absolute = false) */
final class Spinner extends MoonShineComponent
{
    protected string $view = 'moonshine::components.spinner';

    public function __construct(
        public string $size = 'sm',
        public string $color = '',
        public bool $fixed = false,
        public bool $absolute = false,
    ) {
        parent::__construct();
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
