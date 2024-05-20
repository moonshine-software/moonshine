<?php

declare(strict_types=1);

namespace MoonShine\Fields;

final class Markdown extends Textarea
{
    protected string $view = 'moonshine::fields.markdown';

    protected array $assets = [
        'vendor/moonshine/libs/easymde/easymde.min.css',
        'vendor/moonshine/libs/easymde/easymde.min.js',
        'vendor/moonshine/libs/easymde/purify.min.js',
    ];
}
