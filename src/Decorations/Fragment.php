<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

/**
 * @method static static make(array $fields = [])
 */
class Fragment extends Decoration
{
    protected string $view = 'moonshine::decorations.fragment';
}
