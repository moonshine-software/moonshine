<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\BooleanTrait;

class SwitchBoolean extends Field
{
    use BooleanTrait;

    protected static string $component = 'BooleanField';
}
