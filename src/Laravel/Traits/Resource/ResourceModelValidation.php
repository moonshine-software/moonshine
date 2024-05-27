<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * @template-covariant TModel of Model
 */
trait ResourceModelValidation
{
    /**
     * Get an array of validation rules for resource related model
     *
     * @param Model  $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    abstract public function rules(Model $item): array;

    /**
     * Get custom messages for validator errors
     *
     * @return array<string, string[]|string>
     */
    public function validationMessages(): array
    {
        return [];
    }

    /**
     * @param Model  $item
     *
     * @throws Throwable
     */
    public function validate(Model $item): ValidatorContract
    {
        return Validator::make(
            moonshineRequest()->all(),
            $this->rules($item),
            array_merge(
                trans('moonshine::validation'),
                $this->validationMessages()
            ),
            $this->getFormFields()->onlyFields()->extractLabels()
        );
    }

    public function prepareForValidation(): void
    {
        // Logic
    }
}
