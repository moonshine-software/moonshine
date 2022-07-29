<?php

namespace Leeto\MoonShine\Fields;


class WYSIWYG extends Field
{
    protected static string $view = 'wysiwyg';

    protected array $assets = ['vendor/moonshine/js/trix/trix.js', 'vendor/moonshine/css/trix/trix.css'];
}
