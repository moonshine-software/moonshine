<?php

namespace MoonShine\UI;

use Closure;
use MoonShine\Traits\Makeable;

class OffCanvas
{
    use Makeable;

    public function __construct(
        protected ?Closure $title,
        protected ?Closure $content,
        protected bool $isLeft = false
    ) {
    }

    public function isLeft(): bool
    {
        return $this->isLeft;
    }

    public function title(mixed $data = null): ?string
    {
        return call_user_func($this->title, $data);
    }

    public function content(mixed $data = null): ?string
    {
        return call_user_func($this->content, $data);
    }
}
