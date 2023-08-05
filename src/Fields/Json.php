<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Contracts\Fields\RemovableContract;
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
                        $data[$field->column()] = $field->mapKeyValue(
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

    public function vertical(): self
    {
        $this->isVertical = true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }

    protected function resolvePreview(): string
    {
        if ($this->isRawMode()) {
            return (string) $this->toValue();
        }

        return (string) table(
            $this->getFields()->indexFields()->toArray(),
            $this->toValue() ?? []
        )->preview();
    }

    protected function resolveValue(): mixed
    {
        if ($this->isKeyOrOnlyValue()) {
            return collect($this->toValue())
                ->map(
                    fn ($data, $index): array => $this->extractValues(
                        $this->isOnlyValue() ? [$data] : [$index => $data]
                    )
                )
                ->values()
                ->toArray();
        }

        return table($this->getFields()->toArray(), $this->toValue() ?? []);
    }

    public function jsonValues(): array
    {
        # TODO[removeme]
        return $this->value()
            ->rows()
            ->map(fn ($row) => $row->getFields()->getValues()->toArray())
            ->toArray();
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            if ($this->requestValue() === false) {
                $item->{$this->column()} = [];

                return $item;
            }

            $requestValues = $this->requestValue();

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

            $item->{$this->column()} = $this->mapKeyValue(collect($requestValues));

            return $item;
        };
    }
}
