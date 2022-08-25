<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class Password extends Field
{
    public static string $component = 'PasswordField';

    protected array $attributes = ['autocomplete'];
}
