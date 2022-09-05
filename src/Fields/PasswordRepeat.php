<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\ValueEntityContract;

class PasswordRepeat extends Field
{
    public static string $component = 'PasswordField';

    protected array $attributes = ['autocomplete'];

    public function value(): string
    {
        return '';
    }

    public function resolveFill(ValueEntityContract $values): static
    {
        $this->setValue('');

        return $this;
    }

    public function requestValue(): bool
    {
        return false;
    }
}
