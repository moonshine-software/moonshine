<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class Textarea extends Field
{
    protected static string $component = 'Textarea';

    protected array $attributes = ['rows', 'cols'];
}
