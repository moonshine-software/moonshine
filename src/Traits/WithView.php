<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithView
{
    protected static string $view = '';
    protected ?string $customView = null;

    public function getView(): string
    {
        return $this->customView ?? static::$view;
    }

    public function customView(string $customView): self
    {
        $this->customView = $customView;

        return $this;
    }
}
