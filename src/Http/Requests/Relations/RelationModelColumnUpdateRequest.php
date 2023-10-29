<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

class RelationModelColumnUpdateRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        $resource = $this->getField()->getResource();

        if (! in_array(
            'update',
            $resource->getActiveActions(),
            true
        )) {
            return false;
        }

        return $resource->can('update');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            $this->get('field') => $this->get('value'),
        ]);
    }

    public function rules(): array
    {
        return [];
    }
}
