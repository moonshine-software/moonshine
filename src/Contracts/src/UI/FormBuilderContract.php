<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\FormMethod;

/**
 * @template TData of mixed = mixed
 *
 * @mixin Conditionable
 * @mixin HasFieldsContract
 * @mixin HasCasterContract<DataCasterContract<TData>, DataWrapperContract<TData>>
 */
interface FormBuilderContract extends
    ComponentContract,
    HasAsyncContract
{
    /**
     * @param  string  $action
     */
    public function action(string $action): self;

    /**
     * @return   string
     */
    public function getAction(): string;

    public function method(FormMethod $method): self;

    public function getMethod(): FormMethod;

    /**
     * @param  TData  $values
     *
     */
    public function fill(mixed $values = []): static;

    /**
     * @param  TData  $values
     * @param DataCasterContract<TData> $cast
     */
    public function fillCast(mixed $values, DataCasterContract $cast): static;

    /**
     * @return   TData
     *
     */
    public function getValues(): mixed;

    /**
     * @param  (Closure(self $ctx): string)|string  $reactiveUrl
     */
    public function reactiveUrl(Closure|string $reactiveUrl): self;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function submit(?string $label = null, array $attributes = [], ?ActionButtonContract $button = null): self;

    public function getSubmit(): ActionButtonContract;

    public function hideSubmit(): self;

    public function isHideSubmit(): bool;

    /**
     * @param  Closure(TData $values, FieldsContract $fields): bool  $apply
     * @param null|Closure(FieldContract $field): void  $default
     * @param null|Closure(TData $values): TData  $before
     * @param null|Closure(TData $values): void  $after
     */
    public function apply(
        Closure $apply,
        ?Closure $default = null,
        ?Closure $before = null,
        ?Closure $after = null,
        bool $throw = false,
    ): bool;

    public function redirect(?string $uri = null): self;

    public function withoutRedirect(): self;

    public function withoutErrorToast(): self;

    public function precognitive(): self;

    public function isPrecognitive(): bool;

    /**
     * @param  string  $method
     * @param  string[]  $events
     */
    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?CrudResourceContract $resource = null,
    ): self;

    /**
     * @param  string|string[]  $events
     * @param  string[]  $exclude
     */
    public function dispatchEvent(array|string $events, array $exclude = [], bool $withoutPayload = false): self;

    /**
     * @param  string|string[]  $selector
     */
    public function asyncSelector(string|array $selector): self;

    public function download(): self;

    /**
     * Async or precognitive
     *
     * @param  string|string[]|null  $events
     */
    public function switchFormMode(bool $isAsync, string|array|null $events = ''): self;

    public function rawMode(): self;

    public function isRaw(): bool;

    public function errorsAbove(bool $enable = true): self;

    /**
     * @param  Closure(FieldsContract $fields, static $ctx): FieldsContract  $callback
     */
    public function onBeforeFieldsRender(Closure $callback): self;

    public function submitShowWhenAttribute(): self;
}
