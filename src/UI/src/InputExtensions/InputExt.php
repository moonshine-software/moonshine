<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

use MoonShine\UI\Enums\DisplayTextType;

final class InputExt extends InputTextExtension
{
    protected string $view = 'moonshine::form.input-extensions.ext';

    protected function getDisplayTextType(): DisplayTextType
    {
        return DisplayTextType::SUFFIX;
    }
}
