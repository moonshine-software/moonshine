<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
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

        if(! $resource->hasAction(Action::UPDATE)) {
            return false;
        }

        return $resource->can(Ability::UPDATE);
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?FieldContract
    {
        return $this->getResource()
            ?->getIndexFields()
            ?->withoutWrappers()
            ?->findByColumn(
                request()->input('field')
            );
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
            request()->input('field') => request()->input('value'),
        ]);
    }
}
