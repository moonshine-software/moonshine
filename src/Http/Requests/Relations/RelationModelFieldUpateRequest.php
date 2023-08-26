<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

class RelationModelFieldUpateRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        if(!$this->hasResource()) {
            return false;
        }

        if (! in_array(
            'edit',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('update');
    }

    public function rules(): array
    {
        $this->errorBag = $this->getRelationName();

        if(!$this->hasResource()) {
            return [];
        }

        $resource = $this->getResource();

        return $resource->rules($resource->getModel());
    }
}
