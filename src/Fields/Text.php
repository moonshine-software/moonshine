<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\WithInputExtensions;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Text extends Field
{
    use WithInputExtensions;
    use WithMask;

    protected static string $view = 'moonshine::fields.input';

    protected string $type = 'text';
}
