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
     * @param TModel $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    abstract protected function rules(Model $item): array;

    public function getRules(): array
    {
        return $this->rules(
            $this->getItemOrInstance()
        );
    }

    /**
     * Get custom messages for validator errors
     *
     * @return array<string, string[]|string>
     */
    public function validationMessages(): array
    {
        return [];
    }

    public function prepareForValidation(): void
    {
        // Logic
    }
}
