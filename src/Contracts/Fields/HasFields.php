<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

use MoonShine\Fields\Fields;

interface HasFields
{
    public function fields(array $fields): static;

    public function hasFields(): bool;

    public function getFields(mixed $data = null): Fields;
}
