<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\WithMask;

class Url extends Field
{
    use WithMask;

    protected static string $view = 'input';

    protected static string $type = 'url';
}
