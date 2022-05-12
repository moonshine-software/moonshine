<?php

namespace Leeto\MoonShine\Fields;


class Editor extends BaseField
{
    protected static string $view = 'editor';

    protected array $assets = [
        'js' => ['vendor/moonshine/js/trix/trix.js'],
        'css' => ['vendor/moonshine/css/trix/trix.css'],
    ];
}