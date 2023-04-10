<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\MoonShineRouter;

trait WithUriKey
{
    public function uriKey(): string
    {
        return MoonShineRouter::uriKey(static::class);
    }
}
