<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

final class InputCopy extends InputExtension
{
    protected string $view = 'moonshine::form.input-extensions.copy';

    protected array $translates = [
        'copied' => 'moonshine::ui.copied',
    ];

    protected array $xData = [
        <<<'HTML'
          copy(value) {
             navigator.clipboard.writeText(value.replace('{{value}}', $refs.extensionInput.value));
          }
        HTML,
    ];
}
