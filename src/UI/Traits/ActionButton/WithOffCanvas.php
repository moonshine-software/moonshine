<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\ActionButton;

use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\OffCanvas;

trait WithOffCanvas
{
    protected ?Closure $offCanvas = null;

    public function isInOffCanvas(): bool
    {
        return ! is_null($this->offCanvas);
    }

    public function inOffCanvas(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $name = null,
        ?Closure $builder = null,
        iterable $components = [],
    ): static {
        if(is_null($name)) {
            $name = (string) spl_object_id($this);
        }

        $async = $this->purgeAsyncTap();

        $this->offCanvas = static fn (mixed $item, ?DataWrapperContract $data, ActionButtonContract $ctx) => OffCanvas::make(
            title: static fn () => value($title, $item, $ctx) ?? $ctx->getLabel(),
            content: static fn () => value($content, $item, $ctx) ?? '',
            asyncUrl: $async ? $ctx->getUrl($item) : null,
            components: $components
        )
            ->name(value($name, $item, $ctx))
            ->when(
                ! is_null($builder),
                static fn (OffCanvas $offCanvas) => $builder($offCanvas, $ctx)
            );

        return $this->onBeforeRender(
            static fn (ActionButtonContract $ctx): ActionButtonContract => $ctx->toggleOffCanvas(
                value($name, $ctx->getData()?->getOriginal(), $ctx)
            )
        );
    }

    public function getOffCanvas(): ?OffCanvas
    {
        return value($this->offCanvas, $this->getData()?->getOriginal(), $this->getData(), $this);
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
