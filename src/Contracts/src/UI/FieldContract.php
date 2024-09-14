<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;

/**
 * @mixin Conditionable
 */
interface FieldContract extends
    FormElementContract,
    SortableFieldContract,
    HasLinkContract,
    HasBadgeContract,
    HasHintContract
{
    public function defaultMode(): static;

    public function isDefaultMode(): bool;

    public function previewMode(): static;

    public function isPreviewMode(): bool;

    public function rawMode(): static;

    public function isRawMode(): bool;

    public function changePreview(Closure $callback): static;

    public function isPreviewChanged(): bool;

    public function columnSelection(bool $active = true): static;

    public function isColumnSelection(): bool;

    public function nullable(Closure|bool|null $condition = null): static;

    public function isNullable(): bool;

    public function horizontal(): static;

    public function withoutWrapper(mixed $condition = null): static;

    public function hasWrapper(): bool;

    public function insideLabel(): static;

    public function isInsideLabel(): bool;

    public function beforeLabel(): static;

    public function isBeforeLabel(): bool;

    public function onChangeMethod(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
    ): static;

    public function onChangeUrl(
        Closure $url,
        HttpMethod $method = HttpMethod::GET,
        array $events = [],
        ?string $selector = null,
        ?AsyncCallback $callback = null,
    ): static;

    public function beforeRender(Closure $callback): static;

    public function getBeforeRender(): Renderable|string;

    public function afterRender(Closure $callback): static;

    public function getAfterRender(): Renderable|string;

    public function changeRender(Closure $callback): static;

    public function isRenderChanged(): bool;

    public function preview(): Renderable|string;
}
