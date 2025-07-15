<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts;

use MoonShine\Crud\QueryTags\QueryTag;

interface HasQueryTagsContract
{
    public function hasQueryTags(): bool;

    /**
     * @return list<QueryTag>
     */
    public function getQueryTags(): array;
}
