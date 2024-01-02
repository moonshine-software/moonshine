<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Support\Stringable;

/** @method static static make(array $items = []) */
final class Breadcrumbs extends MoonShineComponent
{
    protected string $view = 'moonshine::components.breadcrumbs';

    public function __construct(
        public array $items = [],
    ) {
    }

    public function add(string $link, string $label, ?string $icon = null): self
    {
        $this->items[$link] = str($label)
            ->when(
                $icon,
                fn (Stringable $str) => $str->append(":::$icon")
            )
            ->value();

        return $this;
    }
}
