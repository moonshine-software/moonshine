<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait WithRelationshipsTrait
{
    public function isRelationToOne(): bool
    {
        return static::$toOne ?? false;
    }

    public function isRelationHasOne(): bool
    {
        return static::$hasOne ?? false;
    }
}
