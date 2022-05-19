<?php

namespace Leeto\MoonShine\Fields;


class WYSIWYG extends BaseField
{
    protected static string $view = 'wysiwyg';

    protected array $assets = [
        'js' => ['vendor/moonshine/js/trix/trix.js'],
        'css' => ['vendor/moonshine/css/trix/trix.css'],
    ];
}