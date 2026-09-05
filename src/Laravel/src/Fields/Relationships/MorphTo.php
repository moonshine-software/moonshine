<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Support\DTOs\Select\Options;
use MoonShine\Support\Stringify;
use MoonShine\UI\Exceptions\FieldException;

/**
 * @extends BelongsTo<\Illuminate\Database\Eloquent\Relations\MorphTo>
 */
class MorphTo extends BelongsTo
{
    protected string $view = 'moonshine::fields.relationships.morph-to';

    /**
     * @var array<class-string<Model>, string>
     */
    protected array $types = [];

    /**
     * @var array<class-string<Model>, string>
     */
    protected array $searchColumns = [];

    protected bool $isMorph = true;

    public function getSearchColumn(string $key): string
    {
        return $this->searchColumns[$key];
    }

    /**
     * @param  array<class-string<Model>, string|array{string, string}>  $types
     */
    public function types(array $types): static
    {
        $this->asyncSearch();

        $this->searchColumns = Collection::make($types)
            ->mapWithKeys(
                static fn (
                    string|array $searchColumn,
                    string $type
                ): array => [$type => \is_array($searchColumn) ? $searchColumn[0] : $searchColumn]
            )
            ->all();

        $this->types = Collection::make($types)
            ->mapWithKeys(
                static fn (
                    string|array $searchColumn,
                    string $type
                ): array => [$type => \is_array($searchColumn) ? $searchColumn[1] : class_basename($type)]
            )
            ->all();

        return $this;
    }

    /**
     * @throws FieldException
     */
    public function getTypes(): Options
    {
        if ($this->types === []) {
            throw ModelRelationFieldException::morphTypesRequired();
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
        return (string) request()->getScalar(
            (string) Str::of($this->getNameDot())->replace(
                $this->getColumn(),
                $this->getMorphType()
            ),
            $this->toValue()
        );
    }

    public function getValues(): Options
    {
        $item = $this->getRelatedModel();

        if (blank(data_get($item, $this->getMorphKey()))) {
            return new Options();
        }

        if (\is_null($item)) {
            return new Options();
        }

        if (\is_null($this->getFormattedValueCallback())) {
            $this->setFormattedValueCallback(
                fn (mixed $v): mixed => $v instanceof Model ? $v->{$this->getSearchColumn($v::class)} : null
            );
        }

        return parent::getValues();
    }

    protected function resolvePreview(): string
    {
        $item = $this->getRelatedModel();

        if ($item === null || ! $item->getKey()) {
            return '';
        }

        $value = $item->{$this->getRelationName()};

        if (! $value instanceof Model) {
            return '';
        }

        $column = $this->getSearchColumn($value::class);
        $type = Stringify::value($item->{$this->getMorphType()});

        $preview = Str::of($this->types[$type] ?? $type)
            ->append('(')
            ->append(Stringify::value(data_get($value, $column)))
            ->append(')')
            ->value();

        return $this->isUnescape()
            ? $preview
            : $this->escapeValue($preview);
    }

    public function getTypeValue(): string
    {
        $default = Arr::first(array_keys($this->types));

        return Stringify::value(old(
            $this->getMorphType()
        ) ?? (
            $this->getRelatedModel()->{$this->getMorphType()} ?? $default
        ));
    }

    protected function resolveValue(): string
    {
        if (\is_scalar($this->toValue())) {
            return (string) $this->toValue();
        }

        return Stringify::value($this->getRelatedModel()->{$this->getMorphKey()});
    }

    public function isReactivitySupported(): bool
    {
        return false;
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'types' => $this->getTypes()->toArray(),
            'typeValue' => $this->getTypeValue(),
            'column' => $this->getColumn(),
            'morphType' => $this->getMorphType(),
            'morphTypeName' => Str::of($this->getNameAttribute())->replace($this->getColumn(), $this->getMorphType()),
            'morphTypeAttributes' => $this->getAttributes()
                ->only(['data-level'])
                ->merge([
                    'data-name' => (string) Str::of(
                        Stringify::value($this->getAttribute('data-name', $this->getNameAttribute()))
                    )->replace($this->getColumn(), $this->getMorphType()),
                    'data-column' => $this->getMorphType(),
                ]),
        ];
    }
}
