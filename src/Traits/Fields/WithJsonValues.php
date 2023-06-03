<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasValueExtraction;
use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\Field;
use Throwable;

/**
 * @mixin Field
 */
trait WithJsonValues
{
    /**
     * @throws Throwable
     */
    public function jsonValues(Model $item = null): array
    {
        $values = [];

        if (is_null($item)) {
            foreach ($this->getFields() as $field) {
                $defaultValue = $field->getAttribute('multiple')
                || $field instanceof HasFields
                    ? [] : '';

                if ($field instanceof HasValueExtraction) {
                    $defaultValue = $field->isGroup()
                        ? [$field->extractValues([])]
                        : $field->extractValues([]);
                }

                $values[$field->field()] = $defaultValue;
            }

            return $values;
        }

        $value = $this->formViewValue($item);

        if ($value instanceof Model) {
            $value = [$value];
        }

        $fields = $this->getFields()->formFields();

        try {
            if (is_iterable($value)) {
                foreach ($value as $index => $data) {
                    if (! $data instanceof Model) {
                        $data = (new class () extends Model {
                            protected $guarded = [];
                        })->newInstance($data);
                    }

                    foreach ($fields as $field) {
                        $fieldValue = $field->formViewValue($data);

                        if ($field instanceof BelongsToMany) {
                            $fieldValue = $fieldValue->pluck('id');
                        }

                        $values[$index][$field->field()] = $fieldValue;
                    }
                }
            }
        } catch (Throwable $e) {
            report($e);
            $values = [];
        }

        return $values;
    }
}
