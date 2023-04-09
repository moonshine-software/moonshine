<?php

declare(strict_types=1);

namespace Leeto\MoonShine\InputExtensions;

use Leeto\MoonShine\Traits\WithHtmlAttributes;
use Leeto\MoonShine\Traits\WithView;

final class InputLock extends InputExtension
{
    protected static string $view = 'moonshine::form.input-extensions.lock';

    protected array $xInit = [
        '$refs.extensionInput.disabled=true'
    ];

    protected array $xData = [
        'isLock: true',
        'toggleLock() { this.isLock = ! this.isLock; $refs.extensionInput.disabled = this.isLock;  }'
    ];
}
