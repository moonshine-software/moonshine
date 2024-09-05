<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\Paginator;

interface PaginatorCasterContract
{
    public function cast(): PaginatorContract;
}
