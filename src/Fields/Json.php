<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Traits\Fields\WithFullPageMode;
use MoonShine\Traits\Fields\WithJsonValues;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;
use Throwable;

class Json extends Field implements
    HasFields,
    HasJsonValues,
    HasFullPageMode,
    RemovableContract,
    HasValueExtraction
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

    public function save(Model $item): Model
    {
        if ($this->requestValue() === false) {
            $item->{$this->field()} = [];

            return $item;
        }

        $item->{$this->field()} = $this->mapKeyValue(collect($this->requestValue()));

        return $item;
    }

    protected function mapKeyValue(Collection $collection): array
    {
        if ($this->hasFields()) {
            $fields = $this->getFields()->formFields();
            $collection = $collection->map(function ($data) use ($fields) {
                foreach ($fields as $field) {
                    if ($field instanceof Json && $field->isKeyValue()) {
                        $data[$field->field()] = $field->mapKeyValue(collect($data[$field->field()] ?? []));
                    }
                }

                return $data;
            });
        }

        return $collection->when(
            $this->isKeyValue(),
            static fn ($data) => $data->mapWithKeys(static fn ($data) => [$data['key'] => $data['value']])
        )->toArray();
    }

    public function formViewValue(Model $item): mixed
    {
        if ($this->isKeyValue()) {
            return collect(parent::formViewValue($item))
                ->map(fn ($data, $index) => $this->extractValues([$index => $data]))
                ->values()
                ->toArray();
        }

        return parent::formViewValue($item);
    }

    public function extractValues(array $data): array
    {
        if ($this->isKeyValue()) {
            return [
                'key' => key($data),
                'value' => $data[key($data)] ?? '',
            ];
        }

        return $data;
    }
}
