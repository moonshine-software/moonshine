<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Fields\Field;
use MoonShine\Http\Requests\MoonShineFormRequest;
use Throwable;

final class UpdateColumnFormRequest extends MoonShineFormRequest
{
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        $resource = $this->getResource();

        if (is_null($resource) || is_null($this->getField())) {
            return false;
        }

        if (! in_array(
            'update',
            $resource->getActiveActions(),
            true
        )) {
            return false;
        }

        return $resource->can('update');
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?Field
    {
        return $this->getResource()
            ?->getIndexFields()
            ?->withoutWrappers()
            ?->findByColumn(request('field'));
    }

    /**
     * @return array{field: string[], value: string[]}
     */
    public function rules(): array
    {
        return [
            'field' => ['required'],
            'value' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        request()->merge([
            request('field') => request('value'),
        ]);
    }
}
