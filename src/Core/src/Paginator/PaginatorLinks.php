<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\Paginator\PaginatorLinksContract;

/**
 * @extends Collection<array-key, array<string|bool>>
 */
final class PaginatorLinks extends Collection implements PaginatorLinksContract
{
}
