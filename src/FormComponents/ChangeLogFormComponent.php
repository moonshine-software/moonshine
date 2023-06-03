<?php

declare(strict_types=1);

namespace MoonShine\FormComponents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class ChangeLogFormComponent extends FormComponent
{
    protected static string $view = 'moonshine::form-components.change-log';

    public static function logs(Model $item): Collection
    {
        if (! (property_exists(
            $item,
            'changeLogs'
        ) && $item->changeLogs !== null) || ! $item->changeLogs instanceof Collection) {
            return collect();
        }

        return $item->changeLogs
            ->filter(static fn ($log) => $log->states_after);
    }
}
