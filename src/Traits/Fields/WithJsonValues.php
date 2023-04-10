<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Fields\HasFormViewValue;
use MoonShine\Fields\Field;
use MoonShine\Fields\Json;

/**
 * @mixin Field
 */
trait WithJsonValues
{
    public function jsonValues(Model $item = null): array
    {
        if (is_null($item)) {
            $data = ['id' => ''];

            foreach ($this->getFields() as $field) {
                $data[$field->field()] = '';
            }

            return $data;
        }

        if (! $this instanceof HasFormViewValue) {
            return [];
        }

        if ($this instanceof Json && $this->isKeyValue()) {
            return collect($this->formViewValue($item))
                ->map(fn ($value, $key) => ['key' => $key, 'value' => $value])
                ->values()
                ->toArray();
        }

        if ($this->formViewValue($item) instanceof Collection) {
            return $this->formViewValue($item)->toArray();
        }

        if ($this->formViewValue($item) instanceof Model) {
            return [$this->formViewValue($item)->toArray()];
        }

        return $this->formViewValue($item) ?? [];
    }
}
