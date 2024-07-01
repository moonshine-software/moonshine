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

    /**
     * @param TModel $item
     */
    public function beforeImportFilling(array $data): array
    {
        return $data;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    public function beforeImported(Model $item): Model
    {
        return $item;
    }

    /**
     * @param TModel $item
     *
     * @return TModel
     */
    public function afterImported(Model $item): Model
    {
        return $item;
    }
}
