<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Leeto\MoonShine\Contracts\Actions\ActionContract;

final class FiltersAction extends Action implements ActionContract
{
    protected static string $view = 'moonshine::base.index.shared.filters';

    public function isTriggered(): bool
    {
        return false;
    }

    public function handle(): mixed
    {
        return null;
    }

    public function url(): string
    {
        return '';
    }

    public function render(): string
    {
        return view($this->getView(), [
            'action' => $this,
            'filters' => $this->resource->filters(),
            'resource' => $this->resource,
        ])->render();
    }
}
