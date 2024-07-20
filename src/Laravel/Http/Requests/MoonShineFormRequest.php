<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Resources\ModelResource;
use Throwable;

/**
 * @template-covariant T of ModelResource
 */
class MoonShineFormRequest extends FormRequest
{
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
            $this->errorBag = $this->getResource()?->getUriKey();
            $this->getResource()?->prepareForValidation();
        }

        $this->request = request()->getPayload();
    }

    public function messages(): array
    {
        if ($this->hasResource()) {
            return array_merge(
                __('moonshine::validation'),
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
                ?->getFormFields()
                ?->onlyFields()
                ?->extractLabels()
            : [];
    }

    /** @return ModelResource */
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

    public function getPage(): PageContract
    {
        return moonshineRequest()->getPage();
    }
}
