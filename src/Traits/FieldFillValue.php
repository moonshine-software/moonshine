<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\SlideField;

trait FieldFillValue
{
    public static function fillFields(array $fields, array $values): array
    {
        return collect($fields)->map(function ($field) use ($values) {
            if ($field instanceof Field) {
                if ($field instanceof SlideField) {
                    $field->setValue([$values[$field->fromField], $values[$field->toField]]);
                } else {
                    $field->setValue($values[$field->relation() ?? $field->field()] ?? null);
                }
            }

            return $field;
        })->toArray();
    }
}
