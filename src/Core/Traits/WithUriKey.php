<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Core\AbstractRouter;

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

    public function getUriKey(): string
    {
        return $this->getAlias() ?? AbstractRouter::uriKey(static::class);
    }
}
