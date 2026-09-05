<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Contracts\Support\Jsonable;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

/**
 * @internal
 * @template TData
 *
 */
interface CrudResourceWithResponseModifiersContract
{
    /**
     * @param DataWrapperContract<TData> $item
     */
    public function modifyResponse(DataWrapperContract $item): Jsonable;

    /**
     * @param  iterable<array-key, TData>  $items
     */
    public function modifyCollectionResponse(iterable $items): Jsonable;
}
