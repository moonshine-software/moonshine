<?php

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\CheckboxTrait;

class Checkbox extends Field
{
    use CheckboxTrait;

    protected static string $view = 'checkbox';

    protected static string $type = 'checkbox';
}
