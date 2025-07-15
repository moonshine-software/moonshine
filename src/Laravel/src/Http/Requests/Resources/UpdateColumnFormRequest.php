<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\UI\Contracts\FieldsWrapperContract;
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

        if (\is_null($resource) || \is_null($this->getField())) {
            return false;
        }

        if (! $resource->hasAction(Action::UPDATE)) {
            return false;
        }

        return $resource->can(Ability::UPDATE);
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?FieldContract
    {
        $resource = $this->getResource();

        if (\is_null($resource)) {
            return null;
        }

        $data = $resource->getCastedData();

        if (\is_null($data)) {
            return null;
        }

        $fields = $resource->getIndexFields();
        $fields->each(fn (FieldContract $field): FieldContract => $field instanceof FieldsWrapperContract ? $field->fillData($data) : $field);

        return $fields
            ->withoutWrappers()
            ->findByColumn(
                request()->getScalar('field')
            );
    }

    /**
     * @return array{field: string[], value: string[]}
     */
    public function rules(): array
    {
        if (! $this->getResource() instanceof CrudResource || ! $this->getResource()->getFormPage() instanceof PageContract) {
            return [
                'field' => ['required'],
                'value' => ['required'],
            ];
        }

        $fieldRules = data_get(
            $this->getResource()->getFormPage()->getRules(),
            request()->getScalar('field'),
        );

        $valueRules = ['present'];

        if (\is_string($fieldRules)) {
            $valueRules[] = $valueRules;
        }

        if (\is_array($fieldRules)) {
            $valueRules = array_merge($valueRules, $fieldRules);
        }

        return [
            'field' => ['required'],
            'value' => $valueRules,
        ];
    }

    protected function prepareForValidation(): void
    {
        request()->merge([
            request()->getScalar('field') => request()->getScalar('value'),
        ]);
    }
}
