<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

/**
 * @mixin Conditionable
 * @mixin ComponentContract
 */
interface FormBuilderContract extends
    HasFieldsContract,
    HasCasterContract,
    HasAsyncContract,
    Stringable
{
    public function action(string $action): self;

    public function submit(string $label, array $attributes = []): self;

    public function apply(
        Closure $apply,
        ?Closure $default = null,
        ?Closure $before = null,
        ?Closure $after = null,
        bool $throw = false,
    ): bool;
}
