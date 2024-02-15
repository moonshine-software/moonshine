<?php

namespace MoonShine\UI;

use Closure;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;

/**
 * TODO 3.0 Remove
 * @method static static make(string|Closure|null $title, string|Closure|null $content, bool $isLeft = false, bool $async = false)
 */
class OffCanvas
{
    use Makeable;
    use WithComponentAttributes;
    use HasAsync;

    public function __construct(
        protected string|Closure|null $title,
        protected string|Closure|null $content,
        protected bool $isLeft = false,
        bool $async = false,
    ) {
        if ($async) {
            $this->async('#');
        }
    }

    public function isLeft(): bool
    {
        return $this->isLeft;
    }

    public function title(mixed $data = null): ?string
    {
        return value($this->title, $data);
    }

    public function content(mixed $data = null): ?string
    {
        $content = value($this->content, $data);

        return is_null($content)
            ? null
            : (string) $content;
    }
}
