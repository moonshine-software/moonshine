<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFieldsContract;
use Leeto\MoonShine\Traits\Fields\WithFieldsTrait;
use Throwable;

class Json extends Field implements HasFieldsContract
{
    use WithFieldsTrait;

    protected static string $view = 'json';

    public function indexViewValue(Model $item, bool $container = false): string
    {
        $columns = [];
        $values = $item->{$this->field()};

        if($this->isKeyValue()) {
            $values = collect($item->{$this->field()})
                ->map(fn($value, $key) => ['key' => $key, 'value' => $value]);
        }

        foreach ($this->getFields() as $field) {
            $columns[$field->field()] = $field->label();
        }

        return view('moonshine::shared.table', [
            'columns' => $columns,
            'values' => $values
        ]);
    }

    public function exportViewValue(Model $item): string
    {
        return '';
    }

    public function save(Model $item): Model
    {
        if($this->isKeyValue()) {
            if($this->requestValue() !== false) {
                $item->{$this->field()} = collect($this->requestValue())
                    ->mapWithKeys(fn($data) => [$data['key'] => $data['value']]);
            }

            return $item;
        }

        return parent::save($item);
    }
}