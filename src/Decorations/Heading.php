<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;

/**
 * @method static static make(Closure|string $label)
 */
class Heading extends Decoration
{
    protected string $view = 'moonshine::decorations.heading';
}
