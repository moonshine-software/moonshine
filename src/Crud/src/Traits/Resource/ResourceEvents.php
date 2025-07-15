<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits\Resource;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @template T
 */
trait ResourceEvents
{
    /**
     * @param DataWrapperContract<T> $item
     *
     * @return DataWrapperContract<T>
     */
    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        return $item;
    }


    /**
     * @param DataWrapperContract<T> $item
     *
     * @return DataWrapperContract<T>
     */
    protected function afterCreated(DataWrapperContract $item): DataWrapperContract
    {
        return $item;
    }


    /**
     * @param DataWrapperContract<T> $item
     *
     * @return DataWrapperContract<T>
     */
    protected function beforeUpdating(DataWrapperContract $item): DataWrapperContract
    {
        return $item;
    }


    /**
     * @param DataWrapperContract<T> $item
     *
     * @return DataWrapperContract<T>
     */
    protected function afterUpdated(DataWrapperContract $item): DataWrapperContract
    {
        return $item;
    }


    /**
     * @param DataWrapperContract<T> $item
     *
     * @return DataWrapperContract<T>
     */
    protected function beforeDeleting(DataWrapperContract $item): DataWrapperContract
    {
        return $item;
    }


    /**
     * @param DataWrapperContract<T> $item
     *
     * @return DataWrapperContract<T>
     */
    protected function afterDeleted(DataWrapperContract $item): DataWrapperContract
    {
        return $item;
    }

    /**
     * @param  list<int>  $ids
     */
    protected function beforeMassDeleting(array $ids): void
    {
        // Logic here
    }

    /**
     * @param  list<int>  $ids
     */
    protected function afterMassDeleted(array $ids): void
    {
        // Logic here
    }
}
