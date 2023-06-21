<?php

declare(strict_types=1);

namespace MoonShine\FormComponents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Traits\Models\HasMoonShineChangeLog;

final class ChangeLogFormComponent extends FormComponent
{
    protected static string $view = 'moonshine::form-components.change-log';

    public static function logs(Model $item): Collection
    {
        if (! in_array(
            HasMoonShineChangeLog::class,
            class_uses_recursive($item),
            true
        )) {
            return collect();
        }

        return $item->changeLogs
            ->filter(static fn ($log) => $log->states_after);
    }
}
