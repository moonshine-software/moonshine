<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;

/**
 * @mixin ComponentContract
 */
interface ActionButtonContract extends
    ComponentContract,
    HasLabelContract,
    HasOffCanvasContract,
    HasModalContract,
    HasIconContract
{
    public function getUrl(mixed $data = null): string;

    public function setUrl(Closure|string $url): static;

    public function onClick(Closure $onClick, ?string $modifier = null): static;

    public function bulk(?string $forComponent = null): static;

    public function isBulk(): bool;

    public function getBulkForComponent(): ?string;

    public function getData(): ?DataWrapperContract;

    public function setData(?DataWrapperContract $data = null): static;

    public function onBeforeSet(Closure $onBeforeSet): static;

    public function onAfterSet(Closure $onAfterSet): static;

    public function isInDropdown(): bool;

    public function showInDropdown(): static;

    public function showInLine(): static;

    public function method(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): static;

    public function withSelectorsParams(array $selectors): static;

    public function dispatchEvent(array|string $events): static;

    public function async(
        HttpMethod $method = HttpMethod::GET,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null
    ): static;

    public function disableAsync(): static;

    public function getAsyncMethod(): ?string;

    public function isAsyncMethod(): bool;

    public function isAsync(): bool;

    public function badge(Closure|string|int|float|null $value): static;

    public function primary(Closure|bool|null $condition = null): static;

    public function secondary(Closure|bool|null $condition = null): static;

    public function success(Closure|bool|null $condition = null): static;

    public function warning(Closure|bool|null $condition = null): static;

    public function info(Closure|bool|null $condition = null): static;

    public function error(Closure|bool|null $condition = null): static;
}
