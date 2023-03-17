<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Closure;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithLabel;

final class CustomPage
{
    use Makeable;
    use WithLabel;

    public function __construct(
        string $label,
        protected string $alias,
        protected string $view,
        protected ?Closure $viewData = null,
    ) {
        $this->setLabel($label);
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
        return route((string) str('moonshine')
            ->append('.')
            ->append('custom_page'), $this->alias);
    }
}
