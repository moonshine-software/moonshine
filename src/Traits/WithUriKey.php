<?php

declare(strict_types=1);

namespace MoonShine\Traits;

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
        return $this->getAlias() ?? moonshineRouter()->uriKey(static::class);
    }
}
