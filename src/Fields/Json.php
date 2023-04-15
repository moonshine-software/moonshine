<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use JsonException;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Traits\Fields\WithFullPageMode;
use MoonShine\Traits\Fields\WithJsonValues;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;
use Throwable;

class Json extends Field implements HasFields, HasJsonValues, HasFullPageMode, RemovableContract
{
    use WithJsonValues;
    use WithFields;
    use WithFullPageMode;
    use Removable;

    protected static string $view = 'moonshine::fields.json';

    protected bool $keyValue = false;

    protected bool $group = true;

    /**
     * @throws Throwable
     */
    public function keyValue(string $key = 'Key', string $value = 'Value'): static
    {
        $this->keyValue = true;

        $this->fields([
            Text::make($key, 'key')
                ->customAttributes($this->attributes()->getAttributes()),

            Text::make($value, 'value')
                ->customAttributes($this->attributes()->getAttributes()),
        ]);

        return $this;
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }

    /**
     * @throws JsonException
     */
    public function indexViewValue(Model $item, bool $container = false): string
    {
        $columns = [];
        $values = $item->{$this->field()};

        if (! $this->hasFields()) {
            return (string) json_encode($values, JSON_THROW_ON_ERROR);
        }

        if ($this->isKeyValue()) {
            $values = collect($values)
                ->reject(fn ($value, $key) => ! is_scalar($value))
                ->map(fn ($value, $key) => ['key' => $key, 'value' => $value])
                ->toArray();
        }

        foreach ($this->getFields() as $field) {
            $columns[$field->field()] = $field->label();
        }

        return view('moonshine::ui.table', [
            'columns' => $columns,
            'values' => $values,
        ])->render();
    }

    public function exportViewValue(Model $item): string
    {
        return '';
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue() === false) {
            $item->{$this->field()} = [];

            return $item;
        }

        $item->{$this->field()} = collect($this->requestValue())
            ->when(
                $this->isKeyValue(),
                static fn ($data) => $data->mapWithKeys(fn ($data) => [$data['key'] => $data['value']])
            )
            ->toArray();

        return $item;
    }
}
