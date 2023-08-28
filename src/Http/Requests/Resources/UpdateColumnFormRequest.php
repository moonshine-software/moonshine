<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Fields\Field;
use MoonShine\Http\Requests\MoonshineFormRequest;
use Throwable;

final class UpdateColumnFormRequest extends MoonshineFormRequest
{
    public function authorize(): bool
    {
        $resource = $this->getResource();

        if (is_null($resource) || is_null($this->getField())) {
            return false;
        }

        if (! in_array(
            'edit',
            $resource->getActiveActions(),
            true
        )) {
            return false;
        }

        return $resource->can('update');
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?Field
    {
        return $this->getResource()
            ?->getIndexFields()
            ?->findByColumn($this->get('field'));
    }

    /**
     * @return array{field: string[], value: string[]}
     */
    public function rules(): array
    {
        return [
            'field' => ['required'],
            'value' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            $this->get('field') => $this->get('value'),
        ]);
    }
}
