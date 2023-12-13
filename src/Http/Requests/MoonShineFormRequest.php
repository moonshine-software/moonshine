<?php

namespace MoonShine\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Pages\Page;
use Throwable;

class MoonShineFormRequest extends FormRequest
{
    protected $errorBag = 'crud';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    protected function prepareForValidation(): void
    {
        if ($this->hasResource()) {
            $this->getResource()->prepareForValidation();
        }
    }

    public function messages(): array
    {
        if ($this->hasResource()) {
            return array_merge(
                trans('moonshine::validation'),
                $this->getResource()?->validationMessages() ?? []
            );
        }

        return parent::messages();
    }

    /**
     * @throws Throwable
     */
    public function attributes(): array
    {
        return $this->hasResource()
            ? $this->getResource()
                ->getFormFields()
                ->extractLabels()
            : [];
    }

    public function getResource(): ?ResourceContract
    {
        return moonshineRequest()->getResource();
    }

    public function hasResource(): bool
    {
        return ! is_null($this->getResource());
    }

    /**
     * @throws Throwable
     */
    public function beforeResourceAuthorization(): void
    {
        throw_if(
            ! $this->hasResource(),
            ResourceException::notDeclared()
        );
    }

    public function getPage(): Page
    {
        return moonshineRequest()->getPage();
    }
}
