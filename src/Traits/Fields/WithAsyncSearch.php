<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use MoonShine\Fields\Relationships\ModelRelationField;

trait WithAsyncSearch
{
    protected bool $asyncSearch = false;

    protected ?string $asyncUrl = null;

    protected ?string $asyncSearchColumn = null;

    protected int $asyncSearchCount = 15;

    protected ?Closure $asyncSearchQuery = null;

    protected ?Closure $asyncSearchValueCallback = null;

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

    public function asyncSearchUrl(?string $formName = null): string
    {
        if (! is_null($this->asyncUrl)) {
            return $this->asyncUrl;
        }

        $resourceUri = moonshineRequest()->getResourceUri();

        if ($this->hasParent() && $this->parent() instanceof ModelRelationField) {
            $formName = $this->parent()->getFormName() ?? 'crud';
            $resourceUri = $this->parent()
                ->getResource()
                ->uriKey();
        }

        return to_relation_route(
            'search',
            component: $formName,
            relation: $this->getRelationName(),
            resourceUri: $resourceUri
        );
    }

    public function asyncSearch(
        string $asyncSearchColumn = null,
        int $asyncSearchCount = 15,
        ?Closure $asyncSearchQuery = null,
        ?Closure $asyncSearchValueCallback = null,
        ?string $url = null,
    ): static {
        $this->asyncSearch = true;
        $this->searchable = true;
        $this->asyncSearchColumn = $asyncSearchColumn;
        $this->asyncSearchCount = $asyncSearchCount;
        $this->asyncSearchQuery = $asyncSearchQuery;
        $this->asyncSearchValueCallback = $asyncSearchValueCallback;
        $this->asyncUrl = $url;

        $this->valuesQuery = function (Builder $query) {
            if ($this->getRelatedModel()) {
                return $this->getRelation();
            }

            return $query->whereRaw('1=0');
        };

        return $this;
    }

}
