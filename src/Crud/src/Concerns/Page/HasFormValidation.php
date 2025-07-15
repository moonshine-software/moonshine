<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use Illuminate\Contracts\Validation\ValidationRule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Stringable;

/**
 * @template T of mixed = mixed
 */
trait HasFormValidation
{
    protected bool $errorsAbove = true;

    protected bool $isPrecognitive = false;

    /**
     * Get an array of validation rules for resource related model
     *
     * @param DataWrapperContract<T> $item
     *
     * @return array<string, string[]|string|list<ValidationRule>|list<Stringable>>
     */
    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    /**
     * @return array<string, string[]|string|list<ValidationRule>|list<Stringable>>
     */
    public function getRules(): array
    {
        return $this->rules(
            $this->getResource()->getCaster()->cast(
                $this->getResource()->getItemOrInstance()
            )
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

    public function isPrecognitive(): bool
    {
        return $this->isPrecognitive;
    }
}
