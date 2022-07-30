<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Throwable;

class Json extends Field implements HasFields
{
    use WithFields;

    protected static string $view = 'json';

    protected bool $keyValue = false;

    protected bool $group = true;

    /**
     * @throws Throwable
     */
    public function keyValue(string $key = 'Key', string $value = 'Value'): static
    {
        $this->keyValue = true;

        $this->fields([
            Text::make($key, 'key'),
            Text::make($value, 'value'),
        ]);

        return $this;
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }

    public function indexViewValue(Model $item, bool $container = false): string|\Illuminate\Contracts\View\View
    {
        $columns = [];
        $values = $item->{$this->field()};

        if (!$this->hasFields()) {
            return json_encode($values);
        }

        if ($this->isKeyValue()) {
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
        if ($this->isKeyValue()) {
            if ($this->requestValue() !== false) {
                $item->{$this->field()} = collect($this->requestValue())
                    ->mapWithKeys(fn($data) => [$data['key'] => $data['value']]);
            }

            return $item;
        }

        return parent::save($item);
    }
}
