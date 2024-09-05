<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

/** @method static static make(string $href, string $value, ?string $icon = '', bool $withoutIcon = false, bool $blank = false) */
final class Url extends MoonShineComponent
{
    protected string $view = 'moonshine::components.url';

    public function __construct(
        public string $href,
        public string $value,
        public ?string $icon = 'link',
        public bool $withoutIcon = false,
        public bool $blank = false,
    ) {
        parent::__construct();
    }

    public function withoutIcon(): self
    {
        $this->withoutIcon = true;

        return $this;
    }
}
