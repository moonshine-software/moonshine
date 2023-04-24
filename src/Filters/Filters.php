<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Support\Collection;
use MoonShine\Fields\FormElements;

/**
 * @template TKey of array-key
 * @template Field
 *
 * @extends  Collection<TKey, Filter>
 */
final class Filters extends FormElements
{

}
