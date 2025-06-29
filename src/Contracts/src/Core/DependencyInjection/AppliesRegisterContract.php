<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormElementContract;

/**
 * @template-covariant I of AppliesRegisterContract = AppliesRegisterContract
 * @mixin I
 */
interface AppliesRegisterContract
{
    public function type(string $type): static;

    /**
     * @param  class-string  $for
     */
    public function for(string $for): static;

    /**
     * @return class-string
     */
    public function getFor(): string;

    /**
     * @param  class-string  $for
     */
    public function defaultFor(string $for): static;

    /**
     * @return class-string
     */
    public function getDefaultFor(): string;

    /**
     * @template TField of FieldContract = FieldContract
     * @param TField $field
     * @param  ?class-string  $for
     * @return null|ApplyContract<TField>
     */
    public function findByField(
        FormElementContract $field,
        string $type = 'fields',
        ?string $for = null
    ): ?ApplyContract;

    /**
     * @template TField of FieldContract = FieldContract
     * @param  class-string<TField>  $fieldClass
     * @param  class-string<ApplyContract<TField>>  $applyClass
     */
    public function add(string $fieldClass, string $applyClass): static;

    /**
     * @template TField of FieldContract = FieldContract
     * @param  array<class-string<TField>, class-string<ApplyContract<TField>>>  $data
     */
    public function push(array $data): static;

    /**
     * @template TField of FieldContract = FieldContract
     * @param  class-string<TField>  $fieldClass
     * @return null|ApplyContract<TField>
     */
    public function get(string $fieldClass, ?ApplyContract $default = null): ?ApplyContract;
}
