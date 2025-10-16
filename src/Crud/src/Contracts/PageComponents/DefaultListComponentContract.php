<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\PageComponents;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Contracts\Page\IndexPageContract;

interface DefaultListComponentContract
{
    /**
     * @param  iterable<array-key, mixed>  $items
     */
    public function __invoke(
        IndexPageContract $page,
        iterable $items,
        FieldsContract $fields
    ): ComponentContract;
}
