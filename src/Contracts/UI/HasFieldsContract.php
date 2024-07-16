<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;

interface HasFieldsContract
{
    public function fields(FieldsContract|Closure|array $fields): static;

    public function hasFields(): bool;

    public function getFields(): FieldsContract;

    public function getPreparedFields(): FieldsContract;
}
