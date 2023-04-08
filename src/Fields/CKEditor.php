<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

final class CKEditor extends Textarea
{
    protected static string $view = 'moonshine::fields.ckeditor';

    protected array $assets = [
        'vendor/moonshine/libs/ckeditor/ckeditor.js',
    ];
}
