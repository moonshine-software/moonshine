<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\UI\Fields\Json;
use Throwable;

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
        if ($this->getResource() instanceof CrudResource) {
            $this->errorBag = $this->getResource()->getUriKey();
        }

        if ($this->hasResource() && $this->getResource()->getFormPage() instanceof FormPageContract) {
            $this->getResource()->getFormPage()->prepareForValidation();
        }

        $this->request = request()->getPayload();

        $this->prepareJsonFieldsForValidation();
    }

    protected function prepareJsonFieldsForValidation(): void
    {
        if (! $this->hasResource()) {
            return;
        }

        try {
            $fields = $this->getResource()?->getFormFields()?->onlyFields() ?? [];
        } catch (Throwable) {
            return;
        }

        foreach ($fields as $field) {
            if (! $field instanceof Json) {
                continue;
            }

            $name = $this->getValidationJsonFieldName($field);
            $value = $this->request->get($name);

            if (! \is_string($value) || $value === '') {
                continue;
            }

            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && \is_array($decoded)) {
                $this->request->set($name, $decoded);
            }
        }
    }

    protected function getValidationJsonFieldName(FieldContract $field): string
    {
        return str_replace('[]', '', $field->getNameAttribute());
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

    public function messages(): array
    {
        $page = $this->getResource()?->getFormPage();

        if ($page instanceof FormPageContract && $this->getResource() instanceof CrudResource) {
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
                ?->getFormFields()
                ?->onlyFields()
                ?->extractLabels()
            : [];
    }

    public function getResource(): ?CrudResource
    {
        return moonshineRequest()->getResource();
    }

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
