<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\Support\DTOs\Select\Options;
use MoonShine\UI\Exceptions\FieldException;

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
     * @param  array<class-string<Model>, string>  $types
     * @return $this
     */
    public function types(array $types): static
    {
        $this->asyncSearch();

        $this->searchColumns = collect($types)
            ->mapWithKeys(
                static fn (
                    string $searchColumn,
                    string $type
                ): array => [$type => $searchColumn]
            )
            ->toArray();

        $this->types = collect($types)
            ->mapWithKeys(
                static fn (
                    string $searchColumn,
                    string $type
                ): array => [$type => class_basename($type)]
            )
            ->toArray();

        return $this;
    }

    /**
     * @throws FieldException
     */
    public function getTypes(): Options
    {
        if ($this->types === []) {
            throw new FieldException('Morph types is required');
        }

        return new Options(
            $this->types,
            $this->getTypeValue()
        );
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
            $item->{$this->getMorphType()} = $this->getRequestTypeValue();
            $item->{$this->getMorphKey()} = $this->getRequestValue();

            return $item;
        };
    }

    public function getRequestTypeValue(): string
    {
        return request(
            (string) str($this->getNameDot())->replace(
                $this->getColumn(),
                $this->getMorphType()
            ),
            $this->toValue()
        );
    }

    public function getValues(): Options
    {
        $item = $this->getRelatedModel();

        if (empty(data_get($item, $this->getMorphKey()))) {
            return parent::getValues();
        }

        if (is_null($item)) {
            return parent::getValues();
        }

        if (is_null($this->getFormattedValueCallback())) {
            $this->setFormattedValueCallback(
                fn ($v) => $v->{$this->getSearchColumn($v::class)}
            );
        }

        return parent::getValues();
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

    public function getTypeValue(): string
    {
        $default = Arr::first(array_keys($this->types));

        return old($this->getMorphType()) ?? addslashes(
            (string) ($this->getRelatedModel()->{$this->getMorphType()}
            ?? $default)
        );
    }

    protected function resolveValue(): string
    {
        return (string) $this->getRelatedModel()->{$this->getMorphKey()};
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'types' => $this->getTypes()->toArray(),
            'typeValue' => $this->getTypeValue(),
            'column' => $this->getColumn(),
            'morphType' => $this->getMorphType(),
        ];
    }
}
