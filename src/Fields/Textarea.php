<?php

namespace Leeto\MoonShine\Fields;

class Textarea extends Field
{
    protected static string $view = 'textarea';

    protected array $attributes = ['rows', 'cols'];
}
