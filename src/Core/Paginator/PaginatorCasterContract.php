<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

interface PaginatorCasterContract
{
    public function cast(): PaginatorContract;
}
