<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use Illuminate\Contracts\Validation\Rule;
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
     * @return array<string, string|list<string|ValidationRule|Rule|Stringable>>
     */
    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    /**
     * @return array<string, string|list<string|ValidationRule|Rule|Stringable>>
     */
    public function getRules(): array
    {
        /** @var DataWrapperContract<T> $item */
        $item = $this->getResourceOrFail()->getCaster()->cast(
            $this->getResourceOrFail()->getItemOrInstance()
        );

        return $this->rules($item);
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
