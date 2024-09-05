<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

final class Markdown extends Textarea
{
    protected string $view = 'moonshine::fields.markdown';

    public function getAssets(): array
    {
        return [
            Css::make('vendor/moonshine/libs/easymde/easymde.min.css'),
            Js::make('vendor/moonshine/libs/easymde/easymde.min.js'),
            Js::make('vendor/moonshine/libs/easymde/purify.min.js'),
        ];
    }
}
