<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;
use Leeto\MoonShine\Traits\Fields\Removable;
use Leeto\MoonShine\Contracts\Fields\Removable as RemovableContract;

class Image extends Field implements Fileable, RemovableContract
{
    use CanBeMultiple;
    use FileTrait;
    use Removable;

    public static string $component = 'Image';
}
