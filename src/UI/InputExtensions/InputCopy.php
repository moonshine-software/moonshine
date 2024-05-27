<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

final class InputCopy extends InputExtension
{
    protected string $view = 'moonshine::form.input-extensions.copy';

    protected array $xData = [
        <<<'HTML'
          copy() {
            navigator.clipboard.writeText($refs.extensionInput.value);
          }
        HTML,
    ];
}
