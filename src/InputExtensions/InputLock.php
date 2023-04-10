<?php

declare(strict_types=1);

namespace MoonShine\InputExtensions;

final class InputLock extends InputExtension
{
    protected static string $view = 'moonshine::form.input-extensions.lock';

    protected array $xInit = [
        '$refs.extensionInput.readOnly=true',
    ];

    protected array $xData = [
        'isLock: true',
        'toggleLock() { this.isLock = ! this.isLock; $refs.extensionInput.readOnly = this.isLock;  }',
    ];
}
