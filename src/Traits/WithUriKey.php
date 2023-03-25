<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

use Leeto\MoonShine\MoonShineRouter;

trait WithUriKey
{
    public function uriKey(): string
    {
        return MoonShineRouter::uriKey(static::class);
    }
}
