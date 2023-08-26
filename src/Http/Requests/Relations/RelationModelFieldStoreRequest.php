<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

final class RelationModelFieldStoreRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        if(!$this->hasResource()) {
            return false;
        }

        if (! in_array(
            'create',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('create');
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
