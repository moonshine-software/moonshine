<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use MoonShine\Exceptions\ResourceException;
use Throwable;

class RelationModelColumnUpdateRequest extends RelationModelFieldRequest
{
    /**
     * @throws Throwable
     */
    public function authorize(): bool
    {
        $resource = $this->getField()?->getResource();

        throw_if(
            is_null($resource),
            ResourceException::notDeclared()
        );

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
        request()->merge([
            $this->get('field') => $this->get('value'),
        ]);
    }

    public function rules(): array
    {
        return [];
    }
}
