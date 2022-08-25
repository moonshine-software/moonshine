<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class ID extends Field
{
    public string $column = 'id';

    public string $label = 'ID';

    protected static string $component = 'ID';
}
