<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use MoonShine\Traits\HasDifferentHtmlTag;
use MoonShine\Traits\WithHeadingGradation;

/**
 * @method static static make(Closure|string $label)
 */
class Heading extends Decoration
{
    use HasDifferentHtmlTag;
    use WithHeadingGradation;

    protected string $view = 'moonshine::decorations.heading';
}
