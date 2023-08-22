<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

final class RelationModelFieldStoreRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        if (! in_array(
            'create',
            $this->relationResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->relationResource()->can('create');
    }

    public function rules(): array
    {
        $this->errorBag = $this->getRelationName();

        $relationResource = $this->relationResource();

        return $relationResource->rules(
            $relationResource->getModel()
        );
    }
}
