<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\CheckboxTrait;

class Checkbox extends Field
{
    use CheckboxTrait;

    protected static string $view = 'moonshine::fields.checkbox';

    protected static string $type = 'checkbox';
}
