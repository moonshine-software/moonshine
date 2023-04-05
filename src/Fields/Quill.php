<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

final class Quill extends Field
{
    protected static string $view = 'moonshine::fields.quill';

    protected array $assets = [
        'vendor/moonshine/libs/quill/quill.snow.css',
        'vendor/moonshine/libs/quill/quill.js',
    ];
}
