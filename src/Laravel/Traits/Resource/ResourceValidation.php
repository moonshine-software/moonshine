<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

/**
 * @template-covariant T
 */
trait ResourceValidation
{
    protected bool $errorsAbove = true;

    /**
     * Get an array of validation rules for resource related model
     *
     * @param T $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }

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

    public function hasErrorsAbove(): bool
    {
        return $this->errorsAbove;
    }
}
