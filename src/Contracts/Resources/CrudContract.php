<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Resources;

use Leeto\MoonShine\Fields\Fields;

interface CrudContract
{
    public function fields(): array;

    public function fieldsCollection(): Fields;

    public function filters(): array;

    public function actions(): array;

    public function search(): array;
}
