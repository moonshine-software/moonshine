<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;

/**
 * @method static static make(Closure|string $alt = '', Closure|string|array $items = '', Closure|bool $portrait = false)
 */
final class Carousel extends MoonShineComponent
{

    protected string $view = 'moonshine::components.carousel';

    public function __construct(
        protected Closure|string       $alt = '',
        protected Closure|string|array $items = '',
        protected Closure|bool         $portrait = false,
    ) {
    }

    public function items(Closure|string|array $value): self
    {
        $this->items = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $items = value($this->items, $this);
        return [
            'alt' => value($this->alt, $this),
            'items' => is_array($items) ? $items : [$items],
            'portrait' => value($this->portrait, $this),
        ];
    }
}
