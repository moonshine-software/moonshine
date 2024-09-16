<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

interface HasUriKeyContract
{
    public function alias(string $alias): static;

    public function getAlias(): ?string;

    public function getUriKey(): string;
}
