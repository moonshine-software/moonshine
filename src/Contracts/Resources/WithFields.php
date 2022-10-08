<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Resources;

use Leeto\MoonShine\Fields\Fields;

interface WithFields
{
    public function fields(): array;

    public function fieldsCollection(): Fields;
}
