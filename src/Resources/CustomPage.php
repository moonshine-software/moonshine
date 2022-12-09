<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Closure;
use Leeto\MoonShine\Traits\Makeable;

final class CustomPage
{
    use Makeable;

    public function __construct(
        protected string $label,
        protected string $alias,
        protected string $view,
        protected ?Closure $viewData = null,
    )
    {
    }

    public function label(): string
    {
        return $this->label;
    }

    public function alias(): string
    {
        return $this->alias;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function getViewData(): array
    {
        return is_callable($this->viewData) ? call_user_func($this->viewData) : [];
    }

    public function url(): string
    {
        return route((string) str(config('moonshine.route.prefix'))
            ->append('.')
            ->append('custom_page'), str($this->alias)->slug()->value());
    }
}
