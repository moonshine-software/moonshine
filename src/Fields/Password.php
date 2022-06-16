<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Password extends Field
{
    public static string $view = 'input';

    public static string $type = 'password';

    protected string $autocomplete = 'new-password';

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
        if($this->requestValue()) {
            $item->{$this->field()} = bcrypt($this->requestValue());
        }

        return $item;
    }
}