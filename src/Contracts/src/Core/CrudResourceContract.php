<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @template-covariant TData
 * @template-covariant TIndexPage of PageContract
 * @template-covariant TFormPage of PageContract
 * @template-covariant TDetailPage of PageContract
 * @template-covariant TFields of FieldsContract
 * @template-covariant TItems of iterable
 */
interface CrudResourceContract
{
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
     * @return ?TData
     */
    public function getItem(): mixed;

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
}
