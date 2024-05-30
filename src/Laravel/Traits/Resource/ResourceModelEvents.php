<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Database\Eloquent\Model;

/**
 * @template-covariant TModel of Model
 */
trait ResourceModelEvents
{
    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function beforeCreating(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function afterCreated(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function beforeUpdating(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function afterUpdated(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function beforeDeleting(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
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
     * @param Model  $item
     *
     * @return Model
     */
    protected function beforeForceDeleting(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function afterForceDeleted(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function beforeRestoring(Model $item): Model
    {
        return $item;
    }

    /**
     * @param Model  $item
     *
     * @return Model
     */
    protected function afterRestored(Model $item): Model
    {
        return $item;
    }
}
