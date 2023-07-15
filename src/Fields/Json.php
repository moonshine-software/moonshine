<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Traits\Fields\WithDefaultValue;
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
    HasValueExtraction,
    HasDefaultValue,
    DefaultCanBeArray
{
    use WithJsonValues;
    use WithFields;
    use WithFullPageMode;
    use Removable;
    use WithDefaultValue;

    protected static string $view = 'moonshine::fields.json';

    protected bool $keyValue = false;

    protected bool $onlyValue = false;

    protected bool $group = true;

    /**
     * @throws Throwable
     */
    public function keyValue(
        string $key = 'Key',
        string $value = 'Value'
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;

        $this->fields([
            Text::make($key, 'key')
                ->customAttributes($this->attributes()->getAttributes()),

            Text::make($value, 'value')
                ->customAttributes($this->attributes()->getAttributes()),
        ]);

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function onlyValue(
        string $value = 'Value'
    ): static {
        $this->keyValue = false;
        $this->onlyValue = true;

        $this->fields([
            Text::make($value, 'value')
                ->customAttributes($this->attributes()->getAttributes()),
        ]);

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function save(Model $item): Model
    {
        if ($this->requestValue() === false) {
            $item->{$this->field()} = [];

            return $item;
        }

        $requestValues = $this->requestValue();

        foreach ($requestValues as $index => $values) {
            foreach ($this->getFields()->onlyFileFields() as $field) {
                $field->setParentRequestValueKey(
                    $this->field() . "." . $index
                );

                $requestValues[$index][$field->field()] = $field->hasManyOrOneSave(
                    $values[$field->field()] ?? null
                );
            }
        }

        $item->{$this->field()} = $this->mapKeyValue(collect($requestValues));

        return $item;
    }

    /**
     * @throws Throwable
     */
    protected function mapKeyValue(Collection $collection): array
    {
        if ($this->hasFields()) {
            $fields = $this->getFields()->formFields();
            $collection = $collection->map(function ($data) use ($fields) {
                foreach ($fields as $field) {
                    if ($field instanceof Json && $field->isKeyOrOnlyValue()) {
                        $data[$field->field()] = $field->mapKeyValue(
                            collect($data[$field->field()] ?? [])
                        );
                    }
                }

                return $data;
            });
        }

        return $collection->when(
            $this->isKeyOrOnlyValue(),
            fn ($data): Collection => $data->mapWithKeys(
                fn ($data, $key): array => $this->isOnlyValue()
                    ? [$key => $data['value']]
                    : [$data['key'] => $data['value']]
            )
        )->toArray();
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }

    public function isOnlyValue(): bool
    {
        return $this->onlyValue;
    }

    public function isKeyOrOnlyValue(): bool
    {
        return $this->keyValue || $this->onlyValue;
    }

    public function formViewValue(Model $item): mixed
    {
        if ($this->isKeyOrOnlyValue()) {
            return collect(parent::formViewValue($item))
                ->map(
                    fn ($data, $index): array => $this->extractValues(
                        $this->isOnlyValue() ? [$data] : [$index => $data]
                    )
                )
                ->values()
                ->toArray();
        }

        return parent::formViewValue($item);
    }

    public function extractValues(array $data): array
    {
        if ($this->isKeyValue()) {
            return [
                'key' => key($data) ?? '',
                'value' => $data[key($data)] ?? '',
            ];
        }

        if ($this->isOnlyValue()) {
            return [
                'value' => $data[key($data)] ?? '',
            ];
        }

        return $data;
    }
}
