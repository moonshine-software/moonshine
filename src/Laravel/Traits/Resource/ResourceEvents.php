<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

/**
 * @template-covariant T
 */
trait ResourceEvents
{
    /**
     * @param T $item
     *
     * @return T
     */
    protected function beforeCreating(mixed $item): mixed
    {
        return $item;
    }


    /**
     * @param T $item
     *
     * @return T
     */
    protected function afterCreated(mixed $item): mixed
    {
        return $item;
    }


    /**
     * @param T $item
     *
     * @return T
     */
    protected function beforeUpdating(mixed $item): mixed
    {
        return $item;
    }


    /**
     * @param T $item
     *
     * @return T
     */
    protected function afterUpdated(mixed $item): mixed
    {
        return $item;
    }


    /**
     * @param T $item
     *
     * @return T
     */
    protected function beforeDeleting(mixed $item): mixed
    {
        return $item;
    }


    /**
     * @param T $item
     *
     * @return T
     */
    protected function afterDeleted(mixed $item): mixed
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
