<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithView
{
    protected string $view = '';

    protected ?string $customView = null;

    protected array $customViewData = [];

    public function getView(): string
    {
        return $this->customView ?? $this->view;
    }

    public function getCustomViewData(): array
    {
        return $this->customViewData;
    }

    public function customView(string $view, array $data = []): static
    {
        $this->customView = $view;
        $this->customViewData = $data;

        return $this;
    }
}
