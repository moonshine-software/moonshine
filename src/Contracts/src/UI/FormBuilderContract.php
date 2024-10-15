<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;

/**
 * @mixin Conditionable
 * @mixin HasFieldsContract
 * @mixin HasCasterContract
 */
interface FormBuilderContract extends
    ComponentContract,
    HasAsyncContract
{
    public function action(string $action): self;

    public function submit(string $label, array $attributes = []): self;

    /**
     * @param  Closure(mixed $values, FieldsContract $fields): bool  $apply
     * @param ?Closure(FieldContract $field): void  $default
     * @param ?Closure(mixed $values): mixed  $before
     * @param ?Closure(mixed $values): void  $after
     */
    public function apply(
        Closure $apply,
        ?Closure $default = null,
        ?Closure $before = null,
        ?Closure $after = null,
        bool $throw = false,
    ): bool;
}
