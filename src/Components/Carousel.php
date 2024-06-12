<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;

/**
 * @method static static make(Closure|string $title = '', Closure|string|array $items = '', Closure|bool $album = false)
 */
final class Carousel extends MoonShineComponent
{

    protected string $view = 'moonshine::components.carousel';

    public function __construct(
        protected Closure|string       $title = '',
        protected Closure|string|array $items = '',
        protected Closure|bool         $album = false,
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
            'title' => value($this->title, $this),
            'items' => is_array($items) ? $items : [$items],
            'album' => value($this->album, $this),
        ];
    }
}
