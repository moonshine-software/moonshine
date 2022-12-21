<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

final class CKEditor extends Field
{
    protected static string $view = 'moonshine::fields.ckeditor';

    protected array $assets = [
        'https://cdn.ckeditor.com/ckeditor5/35.3.0/super-build/ckeditor.js',
    ];
}
