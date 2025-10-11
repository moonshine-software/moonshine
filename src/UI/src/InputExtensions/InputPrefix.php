<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

final class InputPrefix extends InputExtension
{
    public function __construct(string $content)
    {
        parent::__construct($content);
        $this->customView('moonshine::form.input-extensions.prefix');
    }
}
