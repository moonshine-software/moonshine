<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class WYSIWYG extends Field
{
    protected static string $view = 'moonshine::fields.wysiwyg';

    protected array $assets = ['vendor/moonshine/js/trix/trix.js', 'vendor/moonshine/css/trix/trix.css'];
}
