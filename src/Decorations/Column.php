<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Traits\WithColumnSpan;

class Column extends Decoration
{
    use WithColumnSpan;

    protected string $view = 'moonshine::decorations.column';
}
