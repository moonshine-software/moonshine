<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

use Illuminate\Database\Eloquent\Model;

interface HasExportViewValue
{
    public function exportViewValue(Model $item): string;
}
