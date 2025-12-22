<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;

/**
 * @template TFields of FieldsContract = FieldsContract
 */
interface HasReactivityContract
{
    public function isReactive(): bool;

    /**
     * @param  array<string, mixed>  $values
     */
    public function isSilentReactive(mixed $value, array $values): bool;

    /**
     * @param  array<string, mixed>  $values
     */
    public function isSilentSelfReactive(mixed $value, array $values): bool;

    public function isReactivitySupported(): bool;

    /**
     * @param  string[]  $except
     */
    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): mixed;

    public function getReactiveValue(): mixed;

    /**
     * @param  TFields  $fields
     * @param  array<string, mixed>  $values
     * @param  array<string, mixed>  $additionally
     *
     * @return TFields
     */
    public function getReactiveCallback(FieldsContract $fields, mixed $value, array $values, array $additionally): FieldsContract;

    /**
     * @param  ?Closure(TFields $fields, mixed $value, static $ctx, array<string, mixed> $values, array<string, mixed> $additionally): TFields  $callback
     * @param  bool|(Closure(mixed $value, array<string, mixed> $values, static $ctx): bool)   $silent
     * @param  bool|(Closure(mixed $value, array<string, mixed> $values, static $ctx): bool)   $silentSelf
     */
    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
        bool|Closure $silent = false,
        bool|Closure $silentSelf = false,
    ): static;
}
