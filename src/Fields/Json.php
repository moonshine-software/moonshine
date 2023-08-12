<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;
use Throwable;

class Json extends Field implements
    HasFields,
    RemovableContract,
    HasValueExtraction,
    HasDefaultValue,
    DefaultCanBeArray
{
    use WithFields;
    use Removable;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.json';

    protected bool $keyValue = false;

    protected bool $onlyValue = false;

    protected bool $isGroup = true;

    protected bool $isVertical = false;

    protected int $level = 0;

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

    public function isKeyValue(): bool
    {
        return $this->keyValue;
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

    public function isOnlyValue(): bool
    {
        return $this->onlyValue;
    }

    public function isKeyOrOnlyValue(): bool
    {
        return $this->keyValue || $this->onlyValue;
    }

    public function vertical(): self
    {
        $this->isVertical = true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
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

    public function values(bool $empty = false): array
    {
        return $this->resolveValue()
            ->rows()
            ->map(
                fn ($row) => $row->getFields()
                    ->when($empty, fn (Fields $fields) => $fields->reset())
                    ->getValues()
                    ->toArray()
            )
            ->toArray();
    }

    public function incrementLevel(): self
    {
        ++$this->level;

        return $this;
    }

    public function level(): int
    {
        return $this->level;
    }

    protected function prepareFields(Fields $fields): Fields
    {
        return $fields->map(function (Field $field): Field {
            throw_if(
                $field instanceof ModelRelationField,
                new FieldException(
                    'Relationship fields in JSON field unavailable'
                )
            );

            $name = str($this->name());

            if ($field instanceof Json) {
                $field = $field->incrementLevel();
            }

            return $field->setName(
                $name
                    ->append('[${index' . $name->substrCount('$') . '}]')
                    ->append("[{$field->column()}]")
                    ->replace('[]', '')
                    ->when(
                        $field->getAttribute('multiple') || $field->isGroup(),
                        static fn (Stringable $str): Stringable => $str->append('[]')
                    )
                    ->value()
            )
                ->setParent($this)
                ->xModel()
            ;
        });
    }

    protected function resolvePreview(): string
    {
        if ($this->isRawMode()) {
            return (string) $this->toFormattedValue();
        }

        return (string) $this->resolveValue()
            ->preview();
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        $values = $raw[$this->column()] ?? [];

        foreach ($this->getFields() as $field) {
            if ($field instanceof HasValueExtraction) {
                foreach ($values as $index => $value) {
                    $values[$index][$field->column()] = collect($value[$field->column()])
                        ->map(fn ($data, $key) => $field->extractValues(
                            $field->isOnlyValue() ? [$data] : [$key => $data]
                        ))
                        ->values()
                        ->toArray();
                }
            }
        }

        return $values;
    }

    protected function resolveValue(): mixed
    {
        return table($this->getFields()->toArray(), $this->toValue() ?? [])
            ->when($this->isVertical(), fn (TableBuilder $table): TableBuilder => $table->vertical());
    }

    /**
     * @throws Throwable
     */
    protected function prepareOnApply(iterable $collection): array
    {
        $collection = collect($collection);

        if ($this->hasFields()) {
            $fields = $this->getFields()->onlyFields();

            $collection = $collection->map(function ($data) use ($fields) {
                foreach ($fields as $field) {
                    if ($field instanceof Json && $field->isKeyOrOnlyValue()) {
                        $data[$field->column()] = $field->prepareOnApply(
                            collect($data[$field->column()] ?? [])
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

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $requestValues = $this->requestValue();

            if ($requestValues === false) {
                $item->{$this->column()} = [];

                return $item;
            }

            foreach ($requestValues as $index => $values) {
                foreach ($this->getFields()->onlyFileFields() as $field) {
                    $field->setRequestKeyPrefix(
                        $this->column() . "." . $index
                    );

                    $requestValues[$index][$field->column()] = $field->hasManyOrOneSave(
                        $values[$field->column()] ?? null
                    );
                }
            }

            data_set($item, $this->column(), $this->prepareOnApply($requestValues));

            return $item;
        };
    }
}
