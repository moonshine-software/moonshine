<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\PageComponents;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Crud\Contracts\Page\FormPageContract;

interface DefaultFormContract
{
    public function __invoke(
        FormPageContract $page,
        string $action,
        ?DataWrapperContract $item,
        FieldsContract $fields,
        bool $isAsync = true,
    ): FormBuilderContract;
}
