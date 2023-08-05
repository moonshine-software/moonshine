<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;

class Password extends Text
{
    protected string $type = 'password';

    protected array $attributes = [
        'type',
        'autocomplete',
        'disabled',
        'readonly',
        'required',
    ];

    protected function resolvePreview(): string
    {
        return '***';
    }

    protected function resolveValue(): string
    {
        return '';
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            if ($this->requestValue()) {
                $item->{$this->column()} = bcrypt($this->requestValue());
            }

            return $item;
        };
    }
}
