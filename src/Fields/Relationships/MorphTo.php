<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class MorphTo extends BelongsTo
{
    protected string $view = 'moonshine::fields.relationships.morph-to';

    protected array $types = [];

    protected array $searchColumns = [];

    public function getSearchColumn(string $key): string
    {
        return $this->searchColumns[$key];
    }

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

    protected function resolveOnSave(): ?Closure
    {
        return function (Model $item): Model {
            $item->{$this->getMorphType()} = $this->requestTypeValue();
            $item->{$this->getMorphKey()} = $this->requestValue();

            return $item;
        };
    }

    public function getMorphType(): string
    {
        return $this->getRelatedModel()
            ->{$this->getRelation()}()
            ->getMorphType();
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

    protected function resolveValue(): string
    {
        return addslashes(
            $this->getRelatedModel()->{$this->getMorphType()}
            ?? Arr::first(array_keys($this->getTypes()))
        );
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getMorphKey(): string
    {
        return $this->getRelatedModel()
            ->{$this->getRelation()}()
            ->getForeignKeyName();
    }

    protected function resolvePreview(): string
    {
        $item = $this->getRelatedModel();

        return str($item?->{$this->getMorphType()})
            ->append('(')
            ->append($item?->{$this->getMorphKey()})
            ->append(')')
            ->value();
    }
}
