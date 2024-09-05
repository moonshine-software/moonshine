<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;

/**
 * @method static static make(Closure|array $items = [], Closure|bool $portrait = false, Closure|string $alt = '')
 */
final class Carousel extends MoonShineComponent
{
    protected string $view = 'moonshine::components.carousel';

    public function __construct(
        protected Closure|array $items = [],
        protected Closure|bool $portrait = false,
        protected Closure|string $alt = '',
    ) {
        parent::__construct();
    }

    /**
     * @param  Closure|string[]  $value
     *
     * @return $this
     */
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
            'portrait' => value($this->portrait, $this),
            'alt' => value($this->alt, $this),
        ];
    }
}
