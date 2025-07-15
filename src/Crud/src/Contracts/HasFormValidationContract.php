<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Stringable;

interface HasFormValidationContract
{
    /**
     * @return array<string, string[]|string|list<ValidationRule>|list<Stringable>>
     */
    public function getRules(): array;

    /**
     * @return array<string, string[]|string>
     */
    public function validationMessages(): array;

    public function prepareForValidation(): void;

    public function hasErrorsAbove(): bool;

    public function isPrecognitive(): bool;
}
