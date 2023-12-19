<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;

final class Template extends Field
{
    protected ?Closure $renderCallback = null;

    protected function resolvePreview(): string
    {
        return '';
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        if($this->isFillChanged()) {
            return value(
                $this->fillCallback,
                $casted ?? $raw,
                $this
            );
        }

        return '';
    }

    public function changeRender(Closure $closure): self
    {
        $this->renderCallback = $closure;

        return $this;
    }

    public function render(): View|string
    {
        return value($this->renderCallback, $this->toValue(), $this) ?? '';
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
