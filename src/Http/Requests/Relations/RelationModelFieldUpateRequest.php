<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

class RelationModelFieldUpateRequest extends RelationModelFieldRequest
{
    public function authorize(): bool
    {
        if (! in_array(
            'edit',
            $this->relationResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->relationResource()->can('update');
    }

    public function rules(): array
    {
        $this->errorBag = $this->getRelationName();

        $relationResource = $this->relationResource();

        return $relationResource->rules($relationResource->getModel());
    }
}
