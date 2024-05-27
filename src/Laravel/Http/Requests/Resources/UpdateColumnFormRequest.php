<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use MoonShine\UI\Fields\Field;
use Throwable;

final class UpdateColumnFormRequest extends MoonShineFormRequest
{
    /**
     * @throws Throwable
     * @throws ResourceException
     */
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
