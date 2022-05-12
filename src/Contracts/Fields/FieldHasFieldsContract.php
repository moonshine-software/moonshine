<?php

namespace Leeto\MoonShine\Contracts\Fields;


use Illuminate\Database\Eloquent\Model;

interface FieldHasFieldsContract
{
    public function hasFields(): bool;

    public function jsonValues(Model $item = null): array;
}