<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

final class RelationModelFieldStoreRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        $resource = $this->getField()->getResource();

        if (! in_array(
            'create',
            $resource->getActiveActions(),
            true
        )) {
            return false;
        }

        return $resource->can('create');
    }

    public function rules(): array
    {
        $this->errorBag = $this->getRelationName();

        $resource = $this->getField()->getResource();

        return $resource->rules($resource->getModel());
    }
}
