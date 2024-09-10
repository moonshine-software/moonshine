<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\Paginator\PaginatorLinkContract;
use MoonShine\Contracts\Core\Paginator\PaginatorLinksContract;

/**
 * @extends<array-key, PaginatorLinkContract>
 */
final class PaginatorLinks extends Collection implements PaginatorLinksContract
{
}
