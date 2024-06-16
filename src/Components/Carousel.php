<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;

/**
 * @method static static make(Closure|array $items = [], Closure|bool $portrait = false, Closure|string $alt = '', )
 */
final class Carousel extends MoonShineComponent
{
    protected string $view = 'moonshine::components.carousel';

    public function __construct(
        protected Closure|array $items = [],
        protected Closure|bool $portrait = false,
        protected Closure|string $alt = '',
    ) {
    }

    public function items(Closure|array $value): self
    {
        $this->items = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'items' => value($this->items, $this),
            'alt' => value($this->alt, $this),
            'portrait' => value($this->portrait, $this),
        ];
    }
}
