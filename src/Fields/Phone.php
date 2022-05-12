<?php

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\NumberFieldTrait;

class Phone extends BaseField
{
    use NumberFieldTrait;

    protected static string $view = 'input';

    protected static string $type = 'tel';
}