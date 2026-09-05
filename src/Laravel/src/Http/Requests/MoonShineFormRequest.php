<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Resources\CrudResource;
use Throwable;

class MoonShineFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    protected function prepareForValidation(): void
    {
        if ($this->getResource() instanceof CrudResource) {
            $this->errorBag = $this->getResource()->getUriKey();
        }

        if ($this->hasResource() && $this->getResource()->getFormPage() instanceof FormPageContract) {
            $this->getResource()->getFormPage()->prepareForValidation();
        }

        $this->request = request()->getPayload();
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            $exception = new ValidationException($validator);

            throw new HttpResponseException(
                response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                ], $exception->status)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * @return array<string, string|array<string>>
     */
    public function messages(): array
    {
        $page = $this->getResource()?->getFormPage();

        if ($page instanceof FormPageContract && $this->getResource() instanceof CrudResource) {
            /** @var array<string, string>|string $messages */
            $messages = __('moonshine::validation');

            return array_merge(
                \is_array($messages) ? $messages : [],
                $page->validationMessages()
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
                ->onlyFields()
                ->extractLabels()
            : [];
    }

    public function getResource(): ?CrudResource
    {
        /** @var CrudResource|null */
        return moonshineRequest()->getResource();
    }

    public function getResourceOrFail(): CrudResource
    {
        return $this->getResource() ?? throw ResourceException::notDeclared();
    }

    /** @phpstan-assert-if-true CrudResource $this->getResource() */
    public function hasResource(): bool
    {
        return ! \is_null($this->getResource());
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
