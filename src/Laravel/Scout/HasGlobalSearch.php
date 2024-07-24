<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Scout;

use Laravel\Scout\Builder;
use Laravel\Scout\Searchable;

/**
 * @mixin Searchable
 */
// todo(packages)-scout move to package
interface HasGlobalSearch
{
    public function searchableQuery(Builder $builder): Builder;

    public function toSearchableResponse(): SearchableResponse;
}
