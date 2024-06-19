<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Stringable;

/** @method static static make(array $items = []) */
final class Breadcrumbs extends MoonShineComponent
{
    protected string $view = 'moonshine::components.breadcrumbs';

    public function __construct(
        public array $items = [],
    ) {
        parent::__construct();
    }

    public function prepend(string $link, string $label = '', ?string $icon = null): self
    {
        $this->items = collect($this->items)
            ->prepend($this->addItem($label, $icon), $link)
            ->toArray();

        return $this;
    }

    public function add(string $link, string $label = '', ?string $icon = null): self
    {
        $this->items = collect($this->items)
            ->put($link, $this->addItem($label, $icon))
            ->toArray();

        return $this;
    }

    private function addItem(string $label, ?string $icon = null): string
    {
        return str($label)
            ->when(
                $icon,
                static fn (Stringable $str) => $str->append(":::$icon")
            )
            ->value();
    }
}
