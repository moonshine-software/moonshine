<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Field;
use Throwable;

/**
 * @mixin Field
 */
trait WithJsonValues
{
    public function jsonValues(Model $item = null): array
    {
        $values = [];

        if (is_null($item)) {
            foreach ($this->getFields() as $field) {
                $values[$field->field()] = '';
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
                    if (!$data instanceof Model) {
                        $data = (new class () extends Model {
                            protected $guarded = [];
                        })->newInstance($data);
                    }

                    foreach ($fields as $field) {
                        $values[$index][$field->field()] = $field->formViewValue($data);
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
