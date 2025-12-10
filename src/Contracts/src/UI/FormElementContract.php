<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

interface FormElementContract extends
    ComponentContract,
    HasQuickFormElementAttributesContract,
    HasLabelContract,
    NowOnContract,
    HasShowWhenContract
{
    public function hasParent(): bool;

    public function getParent(): ?FormElementContract;

    public function setParent(FormElementContract $field): static;

    public function formName(?string $formName = null): static;

    public function getIdentity(?string $index = null): string;

    public function getFormName(): ?string;

    public function getColumn(): string;

    public function setColumn(string $column): static;

    public function virtualColumn(string $column): static;

    public function getVirtualColumn(): string;

    public function getValue(bool $withOld = true): mixed;

    public function setValue(mixed $value = null): static;

    /**
     * @param  Closure(mixed $value, string $name, mixed $default, static $ctx): mixed  $callback
     */
    public function onRequestValue(Closure $callback): static;

    public function getRequestValue(int|string|null $index = null): mixed;

    public function setRequestKeyPrefix(?string $key): static;

    public function hasRequestValue(string|int|null $index = null): bool;

    public function fill(mixed $value = null, ?DataWrapperContract $casted = null, int $index = 0): static;

    public function fillData(mixed $value, int $index = 0): static;

    public function fillCast(mixed $value, ?DataCasterContract $cast = null, int $index = 0): static;

    public function toRawValue(): mixed;

    public function isRawValueModified(): bool;

    public function modifyRawValue(Closure $callback): static;

    public function toValue(bool $withDefault = true): mixed;

    public function toFormattedValue(): mixed;

    public function getFormattedValueCallback(): ?Closure;

    public function getData(): ?DataWrapperContract;

    public function getDefaultIfExists(): mixed;

    public function getRowIndex(): int;

    public function getNameAttribute(?string $index = null): string;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function customWrapperAttributes(array $attributes): static;

    public function getWrapperAttributes(): ComponentAttributesBagContract;

    public function removeWrapperAttribute(string $name): static;

    /**
     * @param  string|array<int|string, bool|string>  $classes
     */
    public function wrapperClass(string|array $classes): static;

    /**
     * @param  string|array<int|string, bool|string>  $styles
     */
    public function wrapperStyle(string|array $styles): static;

    public function removeWrapperClass(string $pattern): static;

    public function changeFill(Closure $callback): static;

    public function afterFill(Closure $callback): static;

    public function isFillChanged(): bool;

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array;

    public function isGroup(): bool;

    public function reset(): static;

    /**
     * @param  null|Closure(static $ctx): static  $callback
     */
    public function refreshAfterApply(?Closure $callback = null): static;

    public function disableRefreshAfterApply(): static;

    public function isOnRefreshAfterApply(): bool;

    public function resolveRefreshAfterApply(): static;

    public function canApply(Closure $canApply): static;

    public function isCanApply(): bool;

    public function apply(Closure $default, mixed $data): mixed;

    /**
     * @param  ?class-string  $for
     */
    public function getApplyClass(string $type = 'fields', ?string $for = null): ?ApplyContract;

    public function beforeApply(mixed $data): mixed;

    public function afterApply(mixed $data): mixed;

    public function afterDestroy(mixed $data): mixed;

    public function onApply(Closure $onApply): static;

    public function hasOnApply(): bool;

    public function onBeforeApply(Closure $onBeforeApply): static;

    public function onAfterApply(Closure $onAfterApply): static;

    public function onAfterDestroy(Closure $onAfterDestroy): static;
}
