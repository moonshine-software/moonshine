<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class WYSIWYG extends Textarea
{
    protected static string $view = 'moonshine::fields.wysiwyg';

    protected array $assets = [
        'vendor/moonshine/libs/trix/trix.js',
        'vendor/moonshine/libs/trix/trix.css',
    ];
}
