<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

interface HasFields
{
    public function fields(array $fields): static;

    public function hasFields(): bool;

    public function getFields(): array;
}
