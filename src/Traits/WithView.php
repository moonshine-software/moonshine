<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithView
{
    protected ?string $customView = null;

    protected static string $view = '';

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
