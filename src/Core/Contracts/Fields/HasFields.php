<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts\Fields;

use Closure;
use MoonShine\UI\Collections\Fields;

interface HasFields
{
    public function fields(Fields|Closure|array $fields): static;

    public function hasFields(): bool;

    public function getFields(): Fields;

    public function preparedFields(): Fields;
}
