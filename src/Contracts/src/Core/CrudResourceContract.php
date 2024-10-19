<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Traversable;

/**
 * @template TData
 * @template-covariant TIndexPage of CrudPageContract
 * @template-covariant TFormPage of CrudPageContract
 * @template-covariant TDetailPage of CrudPageContract
 * @template TFields of FieldsContract
 * @template-covariant TItems of Traversable
 *
 */
interface CrudResourceContract extends ResourceContract
{
    public function getRoute(
        string $name = null,
        DataWrapperContract|int|string|null $key = null,
        array $query = []
    ): string;

    /**
     * @return DataCasterContract<TData>
     */
    public function getCaster(): DataCasterContract;

    /**
     * @return ?DataWrapperContract<TData>
     */
    public function getCastedData(): ?DataWrapperContract;

    /**
     * @return ?TData
     */
    public function getDataInstance(): mixed;

    /**
     * @return ?PageContract<TIndexPage>
     */
    public function getIndexPage(): ?PageContract;

    /**
     * @return ?PageContract<TFormPage>
     */
    public function getFormPage(): ?PageContract;

    /**
     * @return ?PageContract<TDetailPage>
     */
    public function getDetailPage(): ?PageContract;

    /**
     * @return ?PageContract<TIndexPage|TDetailPage|TFormPage>
     */
    public function getActivePage(): ?PageContract;

    /**
     * @return TFields
     */
    public function getIndexFields(): FieldsContract;

    /**
     * @return TFields
     */
    public function getFormFields(bool $withOutside = false): FieldsContract;

    /**
     * @return TFields
     */
    public function getDetailFields(bool $withOutside = false, bool $onlyOutside = false): FieldsContract;

    /**
     * @return ?TData
     */
    public function getItem(): mixed;

    public function getItemID(): int|string|null;

    /**
     * @return TData
     */
    public function getItemOrInstance(): mixed;

    /**
     * @return TItems
     */
    public function getItems(): mixed;

    /**
     * @return ?TData
     */
    public function findItem(bool $orFail = false): mixed;

    /**
     * @param  array<int|string>  $ids
     */
    public function massDelete(array $ids): void;

    /**
     * @param  TData  $item
     * @param ?TFields $fields
     *
     */
    public function delete(mixed $item, ?FieldsContract $fields = null): bool;

    /**
     * @param  TData  $item
     * @param ?TFields $fields
     *
     * @return TData
     */
    public function save(mixed $item, ?FieldsContract $fields = null): mixed;


    public function getIndexPageUrl(array $params = [], ?string $fragment = null): string;

    /**
     * @param DataWrapperContract<TData>|int|string|null $key
     */
    public function getFormPageUrl(
        DataWrapperContract|int|string|null $key = null,
        array $params = [],
        ?string $fragment = null
    ): string;

    /**
     * @param DataWrapperContract<TData>|int|string $key
     */
    public function getDetailPageUrl(
        DataWrapperContract|int|string $key,
        array $params = [],
        ?string $fragment = null
    ): string;

    public function setQueryParams(iterable $params): static;

    public function getQueryParams(): Collection;

    public function getQueryParamsKeys(): array;

    public function hasSearch(): bool;

    /**
     * @param TData $item
     */
    public function modifyResponse(mixed $item): mixed;

    /**
     * @param  iterable<TData>  $items
     */
    public function modifyCollectionResponse(mixed $items): mixed;
}
