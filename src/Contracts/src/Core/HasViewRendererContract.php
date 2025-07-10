<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\Support\Renderable;
use JsonSerializable;
use Stringable;

interface HasViewRendererContract extends
    HasStructureContract,
    Stringable,
    JsonSerializable,
    CanBeEscapedWhenCastToString
{
    public function getView(): string;

    /**
     * @return array<string, mixed>
     */
    public function getCustomViewData(): array;

    /**
     * @param  array<string, mixed> $data
     */
    public function customView(string $view, array $data = []): static;

    public function shouldRender(): bool;

    public function onBeforeRender(Closure $onBeforeRender): static;

    public function render(): Renderable|Closure|string;

    public function flushRenderCache(): static;

    /**
     * @return array<string, mixed>
     */
    public function toStructure(bool $withStates = true): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
