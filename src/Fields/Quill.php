<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

final class Quill extends Field
{
    protected static string $view = 'moonshine::fields.quill';

    protected array $assets = [
        'https://cdn.quilljs.com/1.3.6/quill.snow.css',
        'https://cdn.quilljs.com/1.3.6/quill.js'
    ];
}
