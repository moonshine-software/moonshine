<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\BooleanTrait;
use Leeto\MoonShine\Traits\Fields\CheckboxTrait;

class Checkbox extends Field
{
    use CheckboxTrait;
    use BooleanTrait;

    protected static string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';
}
