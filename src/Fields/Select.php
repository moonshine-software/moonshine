<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;
use Leeto\MoonShine\Traits\Fields\WithOptions;

class Select extends Field
{
    use Searchable, CanBeMultiple, SelectTrait, WithOptions;

    protected static string $view = 'moonshine::fields.select';
}
