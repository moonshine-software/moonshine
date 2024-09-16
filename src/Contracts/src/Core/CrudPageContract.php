<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;

/**
 * @template TFields of FieldsContract
 */
interface CrudPageContract extends PageContract
{
    /**
     * @return TFields
     */
    public function getFields(): FieldsContract;
}
