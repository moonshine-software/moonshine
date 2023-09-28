<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

class RelationModelFieldDeleteRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        $resource = $this->getField()->getResource();

        if (! in_array(
            'delete',
            $resource->getActiveActions(),
            true
        )) {
            return false;
        }

        return $resource->can('delete');
    }

    public function rules(): array
    {
        $this->errorBag = $this->getRelationName();

        $resource = $this->getField()->getResource();

        return $resource->rules($this->getFieldItemOrFail());
    }
}
