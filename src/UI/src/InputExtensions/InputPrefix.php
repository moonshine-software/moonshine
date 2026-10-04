<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

use MoonShine\UI\Enums\DisplayTextType;

final class InputPrefix extends InputTextExtension
{
    public function __construct(string $content)
    {
        parent::__construct($content);
        $this->customView('moonshine::form.input-extensions.prefix');
    }

    protected function getDisplayTextType(): DisplayTextType
    {
        return DisplayTextType::PREFIX;
    }
}
