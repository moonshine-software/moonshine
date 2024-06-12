<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;

/**
 * @method static static make(Closure|string $title = '', Closure|string|array $thumbnail = '', Closure|bool $album = false)
 */
final class Carousel extends MoonShineComponent
{

    protected string $view = 'moonshine::components.carousel';

    public function __construct(
        protected Closure|string $title = '',
        protected Closure|string|array $thumbnail = '',
        protected Closure|bool $album = false,
    ) {
    }

    public function thumbnail(Closure|string|array $value): self
    {
        $this->thumbnail = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'title' => value($this->title, $this),
            'isCarousel' => is_array(value($this->thumbnail, $this)),
            'thumbnail' => value($this->thumbnail, $this),
            'album' => value($this->album, $this),
        ];
    }
}
