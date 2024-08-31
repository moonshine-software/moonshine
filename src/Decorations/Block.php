<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Traits\WithIcon;

class Block extends Decoration
{
    use WithIcon;

    protected string $view = 'moonshine::decorations.block';
}
