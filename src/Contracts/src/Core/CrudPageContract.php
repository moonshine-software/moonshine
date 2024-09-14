<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Support\Enums\PageType;

/**
 * @template TFields of FieldsContract
 */
interface CrudPageContract extends PageContract
{
    public function getPageType(): ?PageType;

    /**
     * @return TFields
     */
    public function getFields(): FieldsContract;
}
