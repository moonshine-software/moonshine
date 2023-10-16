<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithView
{
    protected string $view = '';

    protected ?string $customView = null;

    public function getView(): string
    {
        return $this->customView ?? $this->view;
    }

    public function customView(string $customView): static
    {
        $this->customView = $customView;

        return $this;
    }
}
