<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Support\Facades\Hash;

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
                data_set($item, $this->column(), Hash::make($this->requestValue()));
            }

            return $item;
        };
    }
}
