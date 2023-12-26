<?php

declare(strict_types=1);

namespace MoonShine\InputExtensions;

final class InputLock extends InputExtension
{
    protected string $view = 'moonshine::form.input-extensions.lock';

    protected array $xInit = [
        '$refs.extensionInput.readOnly=true',
    ];

    protected array $xData = [
        'isLock: true',
        <<<'HTML'
          toggleLock() {
            this.isLock = ! this.isLock;
            $refs.extensionInput.readOnly = this.isLock;
            if(!this.isLock) $refs.extensionInput.focus();
          }
        HTML,
    ];
}
