<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class HiddenIds extends Field
{
    protected string $view = 'moonshine::fields.hidden-ids';

    protected string $type = 'hidden';
}
