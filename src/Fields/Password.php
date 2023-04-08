<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Password extends Text
{
    protected string $type = 'password';

    protected array $attributes = ['type', 'autocomplete', 'disabled', 'readonly', 'required'];

    public function exportViewValue(Model $item): string
    {
        return '***';
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return '***';
    }

    public function formViewValue(Model $item): string
    {
        return '';
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue()) {
            $item->{$this->field()} = bcrypt($this->requestValue());
        }

        return $item;
    }
}
