<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class MorphTo extends BelongsTo
{
    protected static string $view = 'moonshine::fields.morph-to';

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

    public function save(Model $item): Model
    {
        $item->{$this->getMorphType($item)} = $this->requestTypeValue($item);
        $item->{$this->getMorphKey($item)} = $this->requestValue();

        return $item;
    }

    public function getMorphType(Model $model): string
    {
        return $model->{$this->relation()}()->getMorphType();
    }

    public function requestTypeValue(Model $item): string
    {
        return request(
            (string) str($this->nameDot())->replace(
                $this->column(),
                $this->getMorphType($item)
            ),
            $this->formTypeValue($item)
        );
    }

    public function formTypeValue(Model $item): string
    {
        return addslashes(
            $item->{$this->getMorphType($item)}
            ?? Arr::first(array_keys($this->getTypes()))
        );
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getMorphKey(Model $model): string
    {
        return $model->{$this->relation()}()->getForeignKeyName();
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return str($item->{$this->getMorphType($item)})
            ->append('(')
            ->append($item->{$this->getMorphKey($item)})
            ->append(')')
            ->value();
    }
}
