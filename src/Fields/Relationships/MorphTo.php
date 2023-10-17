<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\Exceptions\FieldException;

class MorphTo extends BelongsTo
{
    protected string $view = 'moonshine::fields.relationships.morph-to';

    protected array $types = [];

    protected array $searchColumns = [];

    public function getSearchColumn(string $key): string
    {
        return $this->searchColumns[$key];
    }

    /**
     * @param  array<string, string>  $types
     * @return $this
     */
    public function types(array $types): self
    {
        $this->asyncSearch()
            ->required();

        $this->searchColumns = collect($types)
            ->mapWithKeys(
                fn (
                    string $searchColumn,
                    string $type
                ): array => [$type => $searchColumn]
            )
            ->toArray();

        $this->types = collect($types)
            ->mapWithKeys(
                fn (
                    string $searchColumn,
                    string $type
                ): array => [$type => class_basename($type)]
            )
            ->toArray();

        return $this;
    }

    public function getTypes(): array
    {
        if ($this->types === []) {
            throw new FieldException('Morph types is required');
        }

        return $this->types;
    }

    public function getMorphType(): string
    {
        return $this->getRelation()
            ->getMorphType();
    }

    public function getMorphKey(): string
    {
        return $this->getRelation()
            ->getForeignKeyName();
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (Model $item): Model {
            $item->{$this->getMorphType()} = $this->requestTypeValue();
            $item->{$this->getMorphKey()} = $this->requestValue();

            return $item;
        };
    }

    public function requestTypeValue(): string
    {
        return request(
            (string) str($this->nameDot())->replace(
                $this->column(),
                $this->getMorphType()
            ),
            $this->toValue()
        );
    }

    public function values(): array
    {
        $item = $this->getRelatedModel();

        if (empty(data_get($item, $this->getMorphKey()))) {
            return [];
        }

        if (is_null($item)) {
            return [];
        }

        if (is_null($this->formattedValueCallback())) {
            $this->setFormattedValueCallback(
                fn ($v) => $v->{$this->getSearchColumn($v::class)}
            );
        }

        return parent::values();
    }

    protected function resolvePreview(): string
    {
        $item = $this->getRelatedModel();

        if ($item instanceof Model && ! $item->getKey()) {
            return '';
        }

        if (is_null($item)) {
            return '';
        }

        return str($item->{$this->getMorphType()})
            ->append('(')
            ->append(
                $item
                    ->{$this->getRelationName()}
                    ->{$this->getSearchColumn($item->{$this->getRelationName()}::class)}
            )
            ->append(')')
            ->value();
    }

    protected function resolveValue(): string
    {
        return addslashes(
            $this->getRelatedModel()->{$this->getMorphType()}
            ?? Arr::first(array_keys($this->getTypes()))
        );
    }
}
