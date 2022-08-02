<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\DateTrait;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Date extends Field
{
    use DateTrait, WithMask;

    protected static string $view = 'moonshine::fields.input';

    protected static string $type = 'date';

    protected string $format = 'Y-m-d H:i:s';
}
