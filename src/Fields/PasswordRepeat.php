<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\EntityContract;

class PasswordRepeat extends Field
{
    public static string $component = 'PasswordField';

    protected array $attributes = ['autocomplete'];

    public function value(): string
    {
        return '';
    }

    public function resolveFill(EntityContract $values): static
    {
        $this->setValue('');

        return $this;
    }

    public function requestValue(string $prefix = null): bool
    {
        return false;
    }
}
