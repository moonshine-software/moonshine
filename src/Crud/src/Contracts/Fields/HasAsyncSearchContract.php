<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Fields;

use Closure;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\DTOs\Select\Option;

/**
 * @template TData = mixed
 * @template TQuery = mixed
 * @template TRequest = mixed
 * @method Option getAsyncSearchOption(TData $model, ?string $searchColumn = null)
 */
interface HasAsyncSearchContract
{
    public function isAsyncSearch(): bool;

    public function getAsyncSearchColumn(): ?string;

    /**
     * @return (Closure(TQuery, string, TRequest, FieldContract): TQuery)|null
     */
    public function getAsyncSearchQuery(): ?Closure;

    /**
     * @return (Closure(TQuery, string, TRequest, FieldContract): TQuery)|null
     */
    public function getAssociatedWithSearchQuery(): ?Closure;

    public function getAsyncSearchCount(): int;

    /**
     * @return (Closure(TData, FieldContract): mixed)|null
     */
    public function getAsyncSearchValueCallback(): ?Closure;

    /**
     * @param (Closure(TQuery, string, TRequest, FieldContract): TQuery)|null $searchQuery
     * @param (Closure(TData, FieldContract): mixed)|null $formatted
     */
    public function asyncSearch(
        ?string $column = null,
        ?Closure $searchQuery = null,
        ?Closure $formatted = null,
        ?string $associatedWith = null,
        int $limit = 15,
        ?string $url = null,
    ): static;

    public function isAssociatedWith(): bool;
}
