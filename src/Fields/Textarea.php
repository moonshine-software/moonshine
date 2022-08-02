<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class Textarea extends Field
{
    protected static string $view = 'moonshine::fields.textarea';

    protected array $attributes = ['rows', 'cols'];
}
