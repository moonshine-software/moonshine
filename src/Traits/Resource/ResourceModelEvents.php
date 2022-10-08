<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait ResourceModelEvents
{
    protected static function beforeCreating(Model $item)
    {
    }

    protected static function afterCreated(Model $item)
    {
    }

    protected static function beforeUpdating(Model $item)
    {
    }

    protected static function afterUpdated(Model $item)
    {
    }

    protected static function beforeDeleting(Model $item)
    {
    }

    protected static function afterDeleted(Model $item)
    {
    }

    protected static function beforeMassDeleting(array $ids)
    {
    }

    protected static function afterMassDeleted(array $ids)
    {
    }

    protected static function beforeForceDeleting(Model $item)
    {
    }

    protected static function afterForceDeleted(Model $item)
    {
    }

    protected static function beforeRestoring(Model $item)
    {
    }

    protected static function afterRestored(Model $item)
    {
    }
}
