<?php

declare(strict_types=1);

namespace MoonShine\Laravel\QueryTags;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Crud\QueryTags\QueryTag as BaseQueryTag;

/**
 * @method static static make(Closure|string $label, Closure $builder)
 *
 * @extends BaseQueryTag<Builder>
 */
final class QueryTag extends BaseQueryTag
{
}
