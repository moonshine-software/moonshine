<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\OptionProperty;
use MoonShine\Support\Stringify;

trait WithAsyncSearch
{
    protected bool $asyncSearch = false;

    protected ?string $asyncUrl = null;

    protected ?string $asyncSearchColumn = null;

    protected int $asyncSearchCount = 15;

    /** @var null|(Closure((EloquentBuilder<Model>|Relation<Model, Model, mixed>), string, RelationModelFieldRequest, FieldContract): (EloquentBuilder<Model>|Relation<Model, Model, mixed>)) */
    protected ?Closure $asyncSearchQuery = null;

    /**
     * @var (Closure(Model, FieldContract): mixed)|null
     */
    protected ?Closure $asyncSearchValueCallback = null;

    /**
     * @var array{column: string, disk: string, dir: string}|array{}
     */
    protected array $withImage = [];

    protected ?string $associatedWith = null;

    /** @var null|(Closure((EloquentBuilder<Model>|Relation<Model, Model, mixed>), string, RelationModelFieldRequest, FieldContract): (EloquentBuilder<Model>|Relation<Model, Model, mixed>)) */
    protected ?Closure $associatedWithSearchQuery = null;

    public function withImage(string $column, string $disk = '', string $dir = ''): static
    {
        $this->withImage = [
            'column' => $column,
            'disk' => $disk ?: $this->getCore()->getConfig()->getDisk(),
            'dir' => $dir,
        ];

        $this->relatedColumns([$column]);

        return $this;
    }

    /**
     * @phpstan-assert-if-true array{column: non-empty-string, disk: string, dir: string} $this->withImage
     */
    protected function isWithImage(): bool
    {
        return ! empty($this->withImage['column']);
    }

    public function getImageUrl(Model $item): ?string
    {
        if (! $this->isWithImage()) {
            return null;
        }

        $value = data_get($item, $this->withImage['column']);

        if (empty($value)) {
            return null;
        }

        if (is_iterable($value)) {
            $value = Arr::first($value);
        }

        $value = Str::of(Stringify::value($value))
            ->replaceFirst($this->withImage['dir'], '')
            ->trim('/')
            ->prepend($this->withImage['dir'] . '/')
            ->value();

        return $this->getCore()->getStorage(disk: $this->withImage['disk'])->getUrl($value);
    }

    /**
     * @return Collection<array-key, array<string, mixed>|null>
     */
    public function getValuesWithProperties(bool $onlyCustom = false): Collection
    {
        if (! $this->isWithImage()) {
            return new Collection([]);
        }

        return $this->getMemoizeValues()->mapWithKeys(function (Model $item) use ($onlyCustom): array {
            $option = $this->getAsyncSearchOption($item);

            return [
                Stringify::value($item->getKey()) => $onlyCustom
                    ? $option->getProperties()?->toArray()
                    : $option->toArray(),
            ];
        });
    }

    public function isAsyncSearch(): bool
    {
        return $this->asyncSearch;
    }

    public function isAssociatedWith(): bool
    {
        return $this->associatedWith !== null;
    }

    public function getAsyncSearchColumn(): ?string
    {
        return $this->asyncSearchColumn;
    }

    public function getAsyncSearchCount(): int
    {
        return $this->asyncSearchCount;
    }

    /**
     * @return (Closure((EloquentBuilder<Model>|Relation<Model, Model, mixed>), string, RelationModelFieldRequest, FieldContract): (EloquentBuilder<Model>|Relation<Model, Model, mixed>))|null
     */
    public function getAsyncSearchQuery(): ?Closure
    {
        return $this->asyncSearchQuery;
    }

    /**
     * @return (Closure((EloquentBuilder<Model>|Relation<Model, Model, mixed>), string, RelationModelFieldRequest, FieldContract): (EloquentBuilder<Model>|Relation<Model, Model, mixed>))|null
     */
    public function getAssociatedWithSearchQuery(): ?Closure
    {
        return $this->associatedWithSearchQuery;
    }

    /**
     * @return (Closure(Model, FieldContract): mixed)|null
     */
    public function getAsyncSearchValueCallback(): ?Closure
    {
        return $this->asyncSearchValueCallback;
    }

    public function getAsyncSearchUrl(): string
    {
        if (! \is_null($this->asyncUrl)) {
            return $this->asyncUrl;
        }

        $parentName = null;

        if ($this->hasParent()) {
            $parentName = $this->getParent()?->getColumn();
        }

        $resourceUri = $this->getNowOnResource()?->getUriKey() ?? $this->getCore()->getCrudRequest()->getResourceUri();
        /** @var int|string|null $itemID */
        $itemID = data_get($this->getNowOnQueryParams(), 'resourceItem', $this->getCore()->getCrudRequest()->getItemID());

        return $this->getCore()->getRouter()->getEndpoints()->withRelation(
            'async-search',
            resourceItem: $itemID,
            relation: $this->getRelationName(),
            resourceUri: $resourceUri,
            parentField: $parentName,
        );
    }

    public function getAsyncSearchOption(Model $model, ?string $searchColumn = null): Option
    {
        $searchColumn ??= $this->getAsyncSearchColumn();

        $searchColumn ??= '';

        return new Option(
            label: \is_null($this->getAsyncSearchValueCallback())
                ? Stringify::value(data_get($model, $searchColumn, ''))
                : Stringify::value(\call_user_func($this->getAsyncSearchValueCallback(), $model, $this)),
            value: Stringify::value($model->getKey()),
            properties: new OptionProperty($this->getImageUrl($model)),
        );
    }

    /**
     * @param  string|null  $column
     * @param  ?Closure((EloquentBuilder<Model>|Relation<Model, Model, mixed>) $query, string $term, RelationModelFieldRequest $request, FieldContract $field): (EloquentBuilder<Model>|Relation<Model, Model, mixed>)  $searchQuery
     * @param  ?Closure(Model $data, FieldContract $field): mixed  $formatted
     */
    public function asyncSearch(
        ?string $column = null,
        ?Closure $searchQuery = null,
        ?Closure $formatted = null,
        ?string $associatedWith = null,
        int $limit = 15,
        ?string $url = null,
    ): static {
        $this->asyncSearch = true;
        $this->searchable = true;
        $this->asyncSearchColumn = $column;
        $this->asyncSearchCount = $limit;
        $this->asyncSearchQuery = $searchQuery;
        $this->asyncSearchValueCallback = $formatted ?? ($this->getFormattedValueCallback() === null
            ? null
            : fn (Model $model): mixed => value($this->getFormattedValueCallback(), $model, $this->getRowIndex(), $this));
        $this->associatedWith = $associatedWith;
        $this->asyncUrl = $url;

        if ($this->associatedWith) {
            $this->customAttributes([
                'data-associated-with' => $this->getDotNestedToName($this->associatedWith),
            ]);
        }

        $this->valuesQuery = function (Builder $query) {
            if ($this->getRelatedModel()) {
                return $this->getRelation() ?? $query->whereRaw('1=0');
            }

            return $query->whereRaw('1=0');
        };

        return $this;
    }

    /**
     * @param  ?Closure((EloquentBuilder<Model>|Relation<Model, Model, mixed>) $query, string $term, RelationModelFieldRequest $request, FieldContract $field): (EloquentBuilder<Model>|Relation<Model, Model, mixed>)  $searchQuery
     */
    public function associatedWith(string $column, ?Closure $searchQuery = null): static
    {
        $defaultQuery = static fn (Builder $query, string $term, RelationModelFieldRequest $request) => $query->where($column, $request->input($column));

        $this->associatedWithSearchQuery = $searchQuery;

        return $this->asyncSearch(
            searchQuery: $searchQuery ?? $defaultQuery,
            associatedWith: $column,
        );
    }

    public function asyncOnInit(bool $whenOpen = true): static
    {
        return $this->customAttributes([
            'data-async-on-init' => true,
            'data-async-on-init-dropdown' => $whenOpen,
        ]);
    }
}
