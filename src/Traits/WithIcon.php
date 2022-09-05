<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithIcon
{
    protected ?string $icon = null;

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon ?? 'app';
    }
}
