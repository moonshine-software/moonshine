<?php

declare(strict_types=1);

namespace MoonShine\InputExtensions;

final class InputEye extends InputExtension
{
    protected string $view = 'moonshine::form.input-extensions.eye';

    protected array $xInit = [
        '$refs.extensionInput.type=`password`',
    ];

    protected array $xData = [
        'isHidden: true',
        <<<'HTML'
          toggleEye() {
            this.isHidden = ! this.isHidden;
            $refs.extensionInput.type = this.isHidden ? `password` : `text`;
          }
        HTML,
    ];
}
