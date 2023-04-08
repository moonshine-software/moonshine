<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Traits\WithColumnSpan;

class Column extends Decoration
{
    use WithColumnSpan;

    protected static string $view = 'moonshine::decorations.column';
}
