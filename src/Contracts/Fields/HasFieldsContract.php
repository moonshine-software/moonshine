<?php

namespace Leeto\MoonShine\Contracts\Fields;


use Illuminate\Database\Eloquent\Model;

interface HasFieldsContract
{
    public function fields(array $fields): static;

    public function hasFields(): bool;

    public function getFields(): array;

    public function jsonValues(Model $item = null): array;
}
