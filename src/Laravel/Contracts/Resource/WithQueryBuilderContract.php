<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Contracts\Resource;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface WithQueryBuilderContract
{
    public function newQuery(): Builder;

    public function getQuery(): Builder;
}
