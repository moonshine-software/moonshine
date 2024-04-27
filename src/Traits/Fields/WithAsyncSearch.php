<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait WithAsyncSearch
{
    protected bool $asyncSearch = false;

    protected ?string $asyncUrl = null;

    protected ?string $asyncSearchColumn = null;

    protected int $asyncSearchCount = 15;

    protected ?Closure $asyncSearchQuery = null;

    protected ?Closure $asyncSearchValueCallback = null;

    protected array $withImage = [];

    protected ?string $associatedWith = null;

    public function withImage(string $column, string $disk = 'public', string $dir = ''): static
    {
        $this->withImage = [
            'column' => $column,
            'disk' => $disk,
            'dir' => $dir,
        ];

        $this->relatedColumns([$column]);

        return $this;
    }

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

        $value = str($value)
            ->replaceFirst($this->withImage['dir'], '')
            ->trim('/')
            ->prepend($this->withImage['dir'] . '/')
            ->value();

        return Storage::disk($this->withImage['disk'])
            ->url($value);
    }

    public function valuesWithProperties(bool $onlyCustom = false): Collection
    {
        if (! $this->isWithImage()) {
            return collect();
        }

        return $this->getMemoizeValues()->mapWithKeys(function (Model $item) use ($onlyCustom): array {
            $properties = $this->asyncResponseData($item);

            return [
                $item->getKey() => $onlyCustom
                    ? data_get($properties, 'customProperties')
                    : $properties,
            ];
        });
    }

    public function isAsyncSearch(): bool
    {
        return $this->asyncSearch;
    }

    public function asyncSearchColumn(): ?string
    {
        return $this->asyncSearchColumn;
    }

    public function asyncSearchCount(): int
    {
        return $this->asyncSearchCount;
    }

    public function asyncSearchQuery(): ?Closure
    {
        return $this->asyncSearchQuery;
    }

    public function asyncSearchValueCallback(): ?Closure
    {
        return $this->asyncSearchValueCallback;
    }

    public function asyncSearchUrl(): string
    {
        if (! is_null($this->asyncUrl)) {
            return $this->asyncUrl;
        }

        $parentName = null;

        if ($this->hasParent()) {
            $parentName = $this->parent()?->getColumn();
        }

        $resourceUri = moonshineRequest()->getResourceUri();

        return moonshineRouter()->toRelation(
            'search',
            resourceItem: moonshineRequest()->getItemID(),
            relation: $this->getRelationName(),
            resourceUri: $resourceUri,
            parentField: $parentName
        );
    }

    public function asyncResponseData(Model $model, ?string $searchColumn = null): array
    {
        $searchColumn ??= $this->asyncSearchColumn();

        return [
            'label' => is_closure($this->asyncSearchValueCallback())
                ? ($this->asyncSearchValueCallback())($model, $this)
                : data_get($model, $searchColumn, ''),
            'value' => $model->getKey(),
            'customProperties' => [
                'image' => $this->getImageUrl($model),
            ],
        ];
    }

    public function asyncSearch(
        string $asyncSearchColumn = null,
        int $asyncSearchCount = 15,
        ?Closure $asyncSearchQuery = null,
        ?Closure $asyncSearchValueCallback = null,
        ?string $associatedWith = null,
        ?string $url = null,
    ): static {
        $this->asyncSearch = true;
        $this->searchable = true;
        $this->asyncSearchColumn = $asyncSearchColumn;
        $this->asyncSearchCount = $asyncSearchCount;
        $this->asyncSearchQuery = $asyncSearchQuery;
        $this->asyncSearchValueCallback = $asyncSearchValueCallback ?? $this->formattedValueCallback();
        $this->associatedWith = $associatedWith;
        $this->asyncUrl = $url;

        if ($this->associatedWith) {
            $this->customAttributes([
                'data-associated-with' => $this->dotNestedToName($this->associatedWith),
            ]);
        }

        $this->valuesQuery = function (Builder $query) {
            if ($this->getRelatedModel()) {
                return $this->getRelation();
            }

            return $query->whereRaw('1=0');
        };

        return $this;
    }

    public function associatedWith(string $column, ?Closure $asyncSearchQuery = null): static
    {
        $searchQuery = static fn (Builder $query, Request $request) => $query->where($column, $request->get($column));

        return $this->asyncSearch(
            asyncSearchQuery: is_null($asyncSearchQuery) ? $searchQuery : $asyncSearchQuery,
            associatedWith: $column
        );
    }
}
