<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Closure;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use Throwable;

/**
 * @template TCore of CoreContract = CoreContract
 * @template TData of mixed = mixed
 * @template-covariant TIndexPage of null|CrudPageContract = null
 * @template-covariant TFormPage of null|CrudPageContract = null
 * @template-covariant TDetailPage of null|CrudPageContract = null
 * @template TException of Throwable = \Throwable
 * @template TFields of FieldsContract = FieldsContract
 *
 * @extends CrudResourceWithPagesContract<TData, TIndexPage, TFormPage, TDetailPage>
 * @extends CrudResourceWithFieldsContract<TFields>
 * @extends CrudResourceWithResponseModifiersContract<TData>
 * @extends ResourceContract<CrudPageContract, TCore>
 */
interface CrudResourceContract extends
    ResourceContract,
    CrudResourceWithModalsContract,
    CrudResourceWithPagesContract,
    CrudResourceWithFieldsContract,
    CrudResourceWithResponseModifiersContract,
    CrudResourceWithQueryParamsContract,
    CrudResourceWithSearchContract,
    CrudResourceWithActionsContract,
    CrudResourceWithAuthorizationContract,
    HasListComponentContract
{
    public function getColumn(): string;

    public function isAsync(): bool;

    /**
     * @param  DataWrapperContract<TData>|int|string|null  $key
     * @param array<string, int|float|string|null> $query
     */
    public function getRoute(
        ?string $name = null,
        DataWrapperContract|int|string|null $key = null,
        array $query = []
    ): string;

    /**
     * @return DataCasterContract<TData>
     */
    public function getCaster(): DataCasterContract;

    /**
     * @return null|DataWrapperContract<TData>
     */
    public function getCastedData(): ?DataWrapperContract;

    /**
     * @return TData
     */
    public function getDataInstance(): mixed;

    /**
     * @param  null|TData  $item
     */
    public function setItem(mixed $item): static;

    /**
     * @return null|TData
     */
    public function getItem(): mixed;

    /**
     * @return TData
     *
     * @throws TException
     */
    public function getItemOrFail(): mixed;

    /**
     * @return null|Closure(iterable<TData> $items, TableBuilderContract $table): iterable<TData>
     */
    public function getItemsResolver(): ?Closure;

    public function setItemID(int|string|false|null $itemID): static;

    public function getItemID(): int|string|null;

    public function stopGettingItemFromUrl(): static;

    public function isStopGettingItemFromUrl(): bool;

    /**
     * @return TData
     */
    public function getItemOrInstance(): mixed;

    public function isItemExists(): bool;

    /**
     * @return iterable<TData>|Collection<array-key, TData>|LazyCollection<array-key, TData>|CursorPaginator<array-key, TData>|Paginator<array-key, TData>
     */
    public function getItems(): iterable|Collection|LazyCollection|CursorPaginator|Paginator;

    /**
     * @param bool $orFail
     *
     * @return ($orFail is true ? DataWrapperContract<TData> : null|DataWrapperContract<TData>)
     * @throws TException
     */
    public function findItem(bool $orFail = false): ?DataWrapperContract;

    /**
     * @param  array<int|string>  $ids
     */
    public function massDelete(array $ids): void;

    /**
     * @param  DataWrapperContract<TData>  $item
     * @param null|TFields $fields
     *
     */
    public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool;

    /**
     * @param  DataWrapperContract<TData>  $item
     * @param null|TFields $fields
     *
     * @return DataWrapperContract<TData>
     */
    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract;

    public function isRecentlyCreated(): bool;

    public function getRedirectAfterSave(): ?string;

    public function getRedirectAfterDelete(): string;

    public function isDeleteRelationships(): bool;
}
