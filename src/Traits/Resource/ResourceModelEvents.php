<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
trait ResourceModelEvents
{
    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function beforeCreating(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function afterCreated(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function beforeUpdating(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function afterUpdated(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function beforeDeleting(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function afterDeleted(Model $item): Model
    {
        return $item;
    }

    protected function beforeMassDeleting(array $ids): void
    {
        // Logic here
    }

    protected function afterMassDeleted(array $ids): void
    {
        // Logic here
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function beforeForceDeleting(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function afterForceDeleted(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function beforeRestoring(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    protected function afterRestored(Model $item): Model
    {
        return $item;
    }
}
