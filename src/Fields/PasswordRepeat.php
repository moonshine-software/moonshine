<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class PasswordRepeat extends Field
{
    public static string $component = 'PasswordField';

    protected array $attributes = ['autocomplete'];

    public function value(): string
    {
        return '';
    }

    public function requestValue(): bool
    {
        return false;
    }
}
