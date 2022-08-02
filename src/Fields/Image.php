<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;

class Image extends Field implements Fileable
{
    use FileTrait, CanBeMultiple;

    public static string $view = 'moonshine::fields.image';

    public static string $type = 'file';
}
