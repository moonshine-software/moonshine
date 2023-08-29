<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

class RelationModelFieldUpateRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        $resource = $this->getField()->getResource();

        if (! in_array(
            'edit',
            $resource->getActiveActions(),
            true
        )) {
            return false;
        }

        return $resource->can('update');
    }

    public function rules(): array
    {
        $this->errorBag = $this->getRelationName();

        $resource = $this->getField()->getResource();

        return $resource->rules($this->getFieldItemOrFail());
    }
}
