<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\Paginator;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @extends Arrayable<array-key, array<string|bool>>
 */
interface PaginatorLinksContract extends Arrayable
{
}
