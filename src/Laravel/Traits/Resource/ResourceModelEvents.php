<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Database\Eloquent\Model;

/**
 * @template-covariant TModel of Model
 */
trait ResourceModelEvents
{
    protected function beforeCreating(Model $item): Model
    {
        return $item;
    }


    protected function afterCreated(Model $item): Model
    {
        return $item;
    }


    protected function beforeUpdating(Model $item): Model
    {
        return $item;
    }


    protected function afterUpdated(Model $item): Model
    {
        return $item;
    }


    protected function beforeDeleting(Model $item): Model
    {
        return $item;
    }


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
}
