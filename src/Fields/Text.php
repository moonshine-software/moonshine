<?php

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\WithMask;

class Text extends Field
{
    use WithMask;

    public static string $view = 'input';

    public static string $type = 'text';
}
