<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @template-covariant T
 * @template-covariant C of iterable
 */
interface CrudResourceContract
{
    /**
     * @return DataCasterContract<T>
     */
    public function getCaster(): DataCasterContract;

    /**
     * @return ?DataWrapperContract<T>
     */
    public function getCastedData(): ?DataWrapperContract;

    /**
     * @return ?T
     */
    public function getDataInstance(): mixed;

    public function getIndexPage(): ?PageContract;

    public function getFormPage(): ?PageContract;

    public function getDetailPage(): ?PageContract;

    /**
     * @return ?T
     */
    public function getItem(): mixed;

    /**
     * @return C
     */
    public function getItems(): mixed;

    /**
     * @return ?T
     */
    public function findItem(bool $orFail = false): mixed;

    /**
     * @param  array<int|string>  $ids
     */
    public function massDelete(array $ids): void;

    /**
     * @param  T  $item
     *
     */
    public function delete(mixed $item, ?FieldsContract $fields = null): bool;

    /**
     * @param  T  $item
     *
     * @return T
     */
    public function save(mixed $item, ?FieldsContract $fields = null): mixed;
}
