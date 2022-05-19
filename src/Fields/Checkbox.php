<?php

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\FieldWithFieldsTrait;

class Checkbox extends BaseField
{
    use FieldWithFieldsTrait;

    protected static string $view = 'checkbox';

    protected static string $type = 'checkbox';
}