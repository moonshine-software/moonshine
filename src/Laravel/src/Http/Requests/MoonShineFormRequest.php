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

        $fields = $this->getResource()?->getFormFields()?->onlyFields() ?? [];

        $payload = $this->request->all();

        foreach ($fields as $field) {
            if (! $field instanceof Json) {
                continue;
            }

            $name = $this->getValidationJsonFieldName($field);
            $this->decodeJsonFieldPayload($payload, $this->validationNameSegments($name));
        }

        $this->request->replace($payload);
    }

    protected function getValidationJsonFieldName(FieldContract $field): string
    {
        return str_replace('[]', '', $field->getNameAttribute());
    }

    /**
     * @param  array<array-key, mixed>  $payload
     * @param  list<string>  $segments
     */
    protected function decodeJsonFieldPayload(array &$payload, array $segments): void
    {
        if ($segments === []) {
            return;
        }

        $segment = array_shift($segments);

        if ($this->isValidationNameWildcard($segment)) {
            foreach ($payload as &$value) {
                if (\is_array($value)) {
                    $this->decodeJsonFieldPayload($value, $segments);
                }
            }

            return;
        }

        if (! \array_key_exists($segment, $payload)) {
            return;
        }

        if ($segments !== []) {
            if (\is_array($payload[$segment])) {
                $this->decodeJsonFieldPayload($payload[$segment], $segments);
            }

            return;
        }

        if (! \is_string($payload[$segment]) || $payload[$segment] === '') {
            return;
        }

        $decoded = json_decode($payload[$segment], true);

        if (json_last_error() === JSON_ERROR_NONE && \is_array($decoded)) {
            $payload[$segment] = $decoded;
        }
    }

    /**
     * @return list<string>
     */
    protected function validationNameSegments(string $name): array
    {
        preg_match_all('/([^\[\]]+)|\[([^\]]*)\]/', $name, $matches, PREG_SET_ORDER);

        return array_values(array_filter(
            array_map(
                static fn (array $match): string => ($match[1] ?? '') ?: ($match[2] ?? ''),
                $matches,
            ),
            static fn (string $segment): bool => $segment !== '',
        ));
    }

    protected function isValidationNameWildcard(string $segment): bool
    {
        return preg_match('/^\$\{index\d+}$/', $segment) === 1;
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
