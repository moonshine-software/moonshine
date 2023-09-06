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
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;
use Throwable;

class Json extends Field implements
    HasFields,
    RemovableContract,
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

    protected bool $isCreatable = false;

    protected int $level = 0;

    protected bool $asRelation = false;

    protected ?ModelResource $asRelationResource = null;

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

    public function creatable(): self
    {
        $this->isCreatable = true;

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    protected function incrementLevel(): self
    {
        ++$this->level;

        return $this;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function level(): int
    {
        return $this->level;
    }

    /**
     * @throws Throwable
     */
    public function asRelation(ModelResource $resource): self
    {
        $this->asRelation = true;
        $this->asRelationResource = $resource;

        $this->fields(
            $resource->getFormFields()->onlyFields()
        );

        return $this;
    }

    public function isAsRelation(): bool
    {
        return $this->asRelation;
    }

    public function asRelationResource(): ?ModelResource
    {
        return $this->asRelationResource;
    }

    protected function preparedFields(): Fields
    {
        return $this->getFields()->map(function (Field $field): Field {
            throw_if(
                $field instanceof ModelRelationField,
                new FieldException(
                    'Relationship fields in JSON field unavailable'
                )
            );

            $name = str($this->name());
            $level = $name->substrCount('$');

            if ($field instanceof Json) {
                $field->setLevel($level);
            }

            $name = $name
                ->append('[${index' . $level . '}]')
                ->append("[{$field->column()}]")
                ->replace('[]', '')
                ->when(
                    $field->getAttribute('multiple') || $field->isGroup(),
                    static fn (Stringable $str): Stringable => $str->append('[]')
                )->value();

            return $field
                ->setName($name)
                ->iterableAttributes($level)
                ->setParent($this);
        });
    }

    protected function resolvePreview(): string
    {
        if ($this->isRawMode()) {
            return (string) $this->toFormattedValue();
        }

        return (string) $this->resolveValue()
            ->simple()
            ->preview();
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        if ($this->isKeyOrOnlyValue()) {
            return collect($data)->map(fn ($data, $key): array => $this->extractKeyValue(
                $this->isOnlyValue() ? [$data] : [$key => $data]
            ))->values()->toArray();
        }

        return $data;
    }

    protected function extractKeyValue(array $data): array
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

    protected function resolveValue(): mixed
    {
        $emptyRow = $this->isAsRelation()
            ? $this->asRelationResource()?->getModel()
            : [];

        $iterable = is_iterable($this->toValue());
        $values = $iterable
            ? $this->toValue()
            : [$this->toValue() ?? $emptyRow];

        $values = collect($values)->when(
            ! $this->isPreviewMode() && $iterable,
            static fn ($values): Collection => $values->push($emptyRow)
        );

        return TableBuilder::make($this->preparedFields(), $values)
            ->when(
                $this->isAsRelation(),
                fn (TableBuilder $table): TableBuilder => $table
                    ->preview()
                    ->cast($this->asRelationResource()?->getModelCast())
            )
            ->when(
                $this->isVertical(),
                fn (TableBuilder $table): TableBuilder => $table->vertical()
            );
    }

    /**
     * @throws Throwable
     */
    protected function prepareOnApply(iterable $collection): array
    {
        $collection = collect($collection);

        return $collection->when(
            $this->isKeyOrOnlyValue(),
            fn ($data): Collection => $data->mapWithKeys(
                fn ($data, $key): array => $this->isOnlyValue()
                    ? [$key => $data['value']]
                    : [$data['key'] => $data['value']]
            )
        )->filter()->toArray();
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $requestValues = array_filter($this->requestValue() ?: []);
            $applyValues = [];

            foreach ($requestValues as $index => $values) {
                foreach ($this->getFields() as $field) {
                    $field->setRequestKeyPrefix(
                        str("{$this->column()}.$index")->when(
                            $this->requestKeyPrefix(),
                            fn ($str) => $str->prepend("{$this->requestKeyPrefix()}.")
                        )->value()
                    );

                    $apply = $field->apply(
                        fn ($data): mixed => data_set($data, $field->column(), $values[$field->column()]),
                        $values
                    );

                    data_set(
                        $applyValues[$index],
                        $field->column(),
                        data_get($apply, $field->column())
                    );
                }
            }

            if ($this->isAsRelation()) {
                $item->{$this->column()}()->upsert(
                    $this->prepareOnApply($applyValues),
                    $item->{$this->column()}()->getLocalKeyName()
                );

                return $item;
            }

            return data_set(
                $item,
                $this->column(),
                $this->prepareOnApply($applyValues)
            );
        };
    }
}
