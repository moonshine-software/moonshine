<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

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

    public function resolvePreview(): string
    {
        return '***';
    }

    public function value(): string
    {
        return '';
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue()) {
            $item->{$this->column()} = bcrypt($this->requestValue());
        }

        return $item;
    }
}
