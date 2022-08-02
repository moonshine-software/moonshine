<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\WithMask;

class Text extends Field
{
    use WithMask;

    public static string $view = 'moonshine::fields.input';

    public static string $type = 'text';
}
