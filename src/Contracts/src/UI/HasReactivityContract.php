<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;

/**
 * @template TFields of FieldsContract
 */
interface HasReactivityContract
{
    public function isReactive(): bool;

    /**
     * @param  TFields  $fields
     *
     * @return TFields
     */
    public function getReactiveCallback(FieldsContract $fields, mixed $value, array $values): FieldsContract;

    /**
     * @param  ?Closure(TFields $fields, mixed $value, static $ctx, array $values): TFields  $callback
     * @return static
     */
    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
    ): static;
}
