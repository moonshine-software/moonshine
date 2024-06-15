<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 *
 * @extends<TKey, PaginatorLinkContract>
 */
final class PaginatorLinks extends Collection implements PaginatorLinksContract
{
}
