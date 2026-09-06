<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\ActionButton;

use Closure;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\OffCanvasContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\OffCanvas;

trait WithOffCanvas
{
    /**
     * @var null|Closure(mixed, ?DataWrapperContract, static): OffCanvasContract
     */
    protected ?Closure $offCanvas = null;

    /** @phpstan-assert-if-true Closure $this->offCanvas */
    public function isInOffCanvas(): bool
    {
        return ! \is_null($this->offCanvas);
    }

    /**
     * @param  ?Closure(OffCanvasContract $offCanvas, ActionButtonContract $ctx): OffCanvasContract  $builder
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $title
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $content
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $name
     */
    public function inOffCanvas(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $name = null,
        ?Closure $builder = null,
        iterable $components = [],
    ): static {
        $name ??= static fn (mixed $data, ActionButtonContract $ctx): string => Str::random(6) . $ctx->getData()?->getKey();

        $async = $this->purgeAsyncTap();

        $this->offCanvas = static fn (mixed $item, ?DataWrapperContract $data, ActionButtonContract $ctx) => OffCanvas::make(
            title: static fn () => ($title instanceof Closure ? $title($item, $ctx) : $title) ?? $ctx->getLabel(),
            content: static fn () => ($content instanceof Closure ? $content($item, $ctx) : $content) ?? '',
            asyncUrl: $async ? $ctx->getUrl($item) : null,
            components: $components
        )
            ->name(($name instanceof Closure ? $name($item, $ctx) : $name))
            ->when(
                ! \is_null($builder),
                static fn (OffCanvasContract $offCanvas) => $builder instanceof Closure ? $builder($offCanvas, $ctx) : $offCanvas
            );

        return $this->onBeforeRender(
            static fn (ActionButtonContract $ctx): ActionButtonContract => $ctx->toggleOffCanvas(
                $ctx->getComponent()?->getName() ?? ($name instanceof Closure ? $name($ctx->getData()?->getOriginal(), $ctx) : $name)
            )
        );
    }

    public function getOffCanvas(): ?ComponentContract
    {
        if (! $this->isInOffCanvas()) {
            return null;
        }

        return \call_user_func($this->offCanvas, $this->getData()?->getOriginal(), $this->getData(), $this);
    }

    public function toggleOffCanvas(string $name = 'default'): static
    {
        return $this->onClick(
            static fn (): string => "\$dispatch('" . AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, $name) . "')",
            'prevent'
        );
    }

    public function openOffCanvas(): static
    {
        return $this->onClick(static fn (): string => 'toggleCanvas', 'prevent');
    }
}
