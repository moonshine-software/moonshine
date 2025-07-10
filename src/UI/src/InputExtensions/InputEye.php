<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

final class InputEye extends InputExtension
{
    protected string $view = 'moonshine::form.input-extensions.eye';

    /**
     * @var string[]
     */
    protected array $xInit = [
        '$refs.extensionInput.type=`password`',
    ];

    /**
     * @var string[]
     */
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
