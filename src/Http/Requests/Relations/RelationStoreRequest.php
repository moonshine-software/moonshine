<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

final class RelationStoreRequest extends RelationRequest
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
        $this->errorBag = request('_relation');

        $relationResource = $this->relationResource();

        return $relationResource->rules($relationResource->getModel());
    }
}