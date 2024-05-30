<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

use MoonShine\Core\MoonShineRouter;

trait WithUriKey
{
    protected ?string $alias = null;

    public function alias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function uriKey(): string
    {
        return $this->getAlias() ?? MoonShineRouter::uriKey(static::class);
    }
}
