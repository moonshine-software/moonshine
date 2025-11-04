<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;

/**
 * @template TData of mixed = mixed
 * @template TWrapper of DataWrapperContract<TData> = DataWrapperContract
 * @template TModal of  ComponentContract = ComponentContract
 * @template TOffCanvas of  ComponentContract = ComponentContract
 *
 * @extends HasModalContract<TModal>
 * @extends HasOffCanvasContract<TOffCanvas>
 *
 * @mixin Conditionable
 */
interface ActionButtonContract extends
    ComponentContract,
    HasLabelContract,
    HasOffCanvasContract,
    HasModalContract,
    HasIconContract,
    WithBadgeContract
{
    /**
     * @param  TData  $data
     *
     */
    public function getUrl(mixed $data = null): string;

    /**
     * @param  (Closure(TData $original, null|TWrapper<TData> $casted, static $ctx): string)|string  $url
     */
    public function setUrl(Closure|string $url): static;

    /**
     * @param  Closure(ActionButtonContract $ctx): string  $onClick
     */
    public function onClick(Closure $onClick, ?string $modifier = null): static;

    public function bulk(?string $forComponent = null): static;

    public function isBulk(): bool;

    public function getBulkForComponent(): ?string;

    /**
     * @return null|TWrapper<TData>
     */
    public function getData(): ?DataWrapperContract;

    /**
     * @param  null|TWrapper<TData>  $data
     *
     */
    public function setData(?DataWrapperContract $data = null): static;

    /**
     * @param  Closure(?DataWrapperContract<TData> $data, ActionButtonContract $ctx): ?DataWrapperContract<TData>  $onBeforeSet
     */
    public function onBeforeSet(Closure $onBeforeSet): static;

    /**
     * @param  Closure(null|TWrapper<TData> $data, ActionButtonContract $ctx): void  $onAfterSet
     */
    public function onAfterSet(Closure $onAfterSet): static;

    public function isInDropdown(): bool;

    public function showInDropdown(): static;

    public function showInLine(): static;

    public function blank(): static;

    /**
     * @param array<string, mixed>|(Closure(TData $original): array<string, mixed>) $params = []
     * @param null|string|string[] $selector
     * @param string[] $events
     */
    public function method(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        null|string|array $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): static;

    /**
     * @param  string[]  $selectors
     */
    public function withSelectorsParams(array $selectors): static;

    /**
     * @param  string[]|string  $events
     */
    public function dispatchEvent(array|string $events): static;

    /**
     * @param  null|string|string[]  $selector
     * @param  string[]  $events
     */
    public function async(
        HttpMethod $method = HttpMethod::GET,
        null|string|array $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null
    ): static;

    public function disableAsync(): static;

    public function getAsyncMethod(): ?string;

    public function isAsyncMethod(): bool;

    public function isAsync(): bool;

    public function purgeAsync(): void;

    public function withQueryParams(): static;

    public function hasComponent(): bool;

    public function getComponent(): ?ComponentContract;

    public function download(): static;

    public function rawMode(): self;

    public function isRaw(): bool;

    public function withoutLoading(Closure|bool|null $condition = null): static;

    public function content(Closure|string $content): static;

    public function primary(Closure|bool|null $condition = null): static;

    public function secondary(Closure|bool|null $condition = null): static;

    public function success(Closure|bool|null $condition = null): static;

    public function warning(Closure|bool|null $condition = null): static;

    public function info(Closure|bool|null $condition = null): static;

    public function error(Closure|bool|null $condition = null): static;

    public function square(Closure|bool|null $condition = null): static;

    /**
     * @param non-empty-array<string> $keys
     */
    public function hotKeys(array $keys, bool $withBadge = false): static;
}
