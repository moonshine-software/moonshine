<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Traits\Fields\UpdateOnPreview;

class SwitchBoolean extends Checkbox
{
    use UpdateOnPreview;

    protected string $view = 'moonshine::fields.switch';
}
