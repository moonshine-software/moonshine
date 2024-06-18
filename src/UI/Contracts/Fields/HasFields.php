<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Fields;

use Closure;
use MoonShine\UI\Contracts\Collections\FieldsCollection;

interface HasFields
{
    public function fields(FieldsCollection|Closure|array $fields): static;

    public function hasFields(): bool;

    public function getFields(): FieldsCollection;

    public function getPreparedFields(): FieldsCollection;
}
