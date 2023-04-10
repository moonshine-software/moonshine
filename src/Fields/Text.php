<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Traits\Fields\WithInputExtensions;
use MoonShine\Traits\Fields\WithMask;

class Text extends Field
{
    use WithInputExtensions;
    use WithMask;

    protected static string $view = 'moonshine::fields.input';

    protected string $type = 'text';
}
