<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Traits\WithColumnSpan;

class Block extends Decoration
{
    use WithColumnSpan;

    public static string $view = 'moonshine::decorations.block';
}
