<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\Exceptions\FieldException;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\MorphTo>
 */
class MorphTo extends BelongsTo
{
    protected string $view = 'moonshine::fields.relationships.morph-to';

    protected array $types = [];

    protected array $searchColumns = [];

    protected bool $isMorph = true;

    public function getSearchColumn(string $key): string
    {
        return $this->searchColumns[$key];
    }

    /**
     * @param  array<string, string|array>  $types
     * @return $this
     */
    public function types(array $types): self
    {
        $this->asyncSearch();

        $this->searchColumns = collect($types)
            ->mapWithKeys(
                fn (
                    string|array $searchColumn,
                    string $type
                ): array => [
                    $type => is_array($searchColumn) ? $searchColumn[0] : $searchColumn,
                ]
            )
            ->toArray();

        $this->types = collect($types)
            ->mapWithKeys(
                fn (
                    string|array $searchColumn,
                    string $type
                ): array => [
                    $type => is_array($searchColumn) ? $searchColumn[1] : class_basename($type),
                ]
            )
            ->toArray();

        return $this;
    }

    /**
     * @throws FieldException
     */
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
            ?->getMorphType() ?? '';
    }

    public function getMorphKey(): string
    {
        return $this->getRelation()
            ?->getForeignKeyName() ?? '';
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
        return request()->input(
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

        if (is_null($item) || is_null($item->{$this->getRelationName()})) {
            return '';
        }

        return str($this->types[$item->{$this->getMorphType()}] ?? $item->{$this->getMorphType()})
            ->append('(')
            ->append(
                $item
                    ->{$this->getRelationName()}
                    ->{$this->getSearchColumn($item->{$this->getRelationName()}::class)}
            )
            ->append(')')
            ->value();
    }

    /**
     * @throws FieldException
     */
    public function typeValue(): string
    {
        $default = Arr::first(array_keys($this->getTypes()));

        return old($this->getMorphType()) ?? addslashes(
            $this->getRelatedModel()->{$this->getMorphType()}
            ?? $default
        );
    }

    protected function resolveValue(): string
    {
        return (string) $this->getRelatedModel()->{$this->getMorphKey()};
    }
}
