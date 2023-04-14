<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\MoonShineRequest;

trait WithAsyncSearch
{
    protected bool $asyncSearch = false;

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

    public function asyncSearchCount(): ?int
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

    public function asyncSearch(
        string $asyncSearchColumn = null,
        int $asyncSearchCount = 15,
        ?Closure $asyncSearchQuery = null,
        ?Closure $asyncSearchValueCallback = null
    ): static {
        $this->asyncSearch = true;
        $this->searchable = true;
        $this->asyncSearchColumn = $asyncSearchColumn;
        $this->asyncSearchCount = $asyncSearchCount;
        $this->asyncSearchQuery = $asyncSearchQuery;
        $this->asyncSearchValueCallback = $asyncSearchValueCallback;

        $this->valuesQuery = function (Builder $query) {

            $request = app(MoonShineRequest::class);

            if ($request->getId()) {
                return $request->getItem()->{$this->relation()}();
            }

            return $query->whereRaw('1=0');
        };

        return $this;
    }

}
