<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\WithMask;

class Email extends Field
{
    use WithMask;

    protected static string $component = 'EmailField';
}
