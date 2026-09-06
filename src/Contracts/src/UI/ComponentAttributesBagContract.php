<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\View\ComponentAttributeBag;

/**
 * @mixin ComponentAttributeBag
 * @method self class(mixed $classList)
 * @method self style(mixed $styleList)
 * @method self merge(array<string, mixed> $attributeDefaults = [], bool $escape = true)
 * @method self filter((callable(mixed, string): bool) $callback)
 * @method self except(mixed $keys)
 * @method self only(mixed $keys)
 * @method self whereStartsWith(string|string[] $needles)
 * @method self whereDoesntStartWith(string|string[] $needles)
 * @method array<string, mixed> getAttributes()
 * @method array<string, mixed> jsonSerialize()
 */
interface ComponentAttributesBagContract
{
    public function concat(string $name, string $value, string $separator = ' '): void;

    public function set(string $name, string|bool $value): void;

    public function remove(string $name): void;
}
