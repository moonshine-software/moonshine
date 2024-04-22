<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithView;
use Throwable;

abstract class MoonShineComponent extends Component implements MoonShineRenderable, CanBeEscapedWhenCastToString
{
    use Conditionable;
    use Macroable;
    use Makeable;
    use WithView;
    use HasCanSee;

    protected ?Closure $onBeforeRenderCallback = null;

    public function name(string $name): static
    {
        $this->componentName = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return str_starts_with($this->componentName ?? '', 'moonshine::')
            ? 'default'
            : $this->componentName;
    }

    public function customAttributes(array $attributes): static
    {
        if (! $this->attributes instanceof ComponentAttributeBag) {
            $this->attributes = $this->newAttributeBag($attributes);
        } else {
            $this->attributes = $this->attributes->merge($attributes);
        }

        return $this;
    }

    public function class(string $class): static
    {
        return $this->customAttributes([
            'class' => $class,
        ]);
    }

    public function removeAttribute(string $name): static
    {
        $attributes = array_filter(
            collect($this->attributes)->toArray(),
            static fn ($key): bool => $key !== $name,
            ARRAY_FILTER_USE_KEY
        );

        $this->attributes = $this->newAttributeBag($attributes);

        return $this;
    }

    public function attributes(): ComponentAttributeBag
    {
        return $this->attributes ?: $this->newAttributeBag();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [];
    }

    public function data(): array
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        return array_merge($this->extractPublicProperties(), [
            'attributes' => $this->attributes(),
            'name' => $this->getName(),
        ]);
    }

    public function onBeforeRender(Closure $onBeforeRender): static
    {
        $this->onBeforeRenderCallback = $onBeforeRender;

        return $this;
    }

    public function render(): View|Closure|string
    {
        $mergeData = [
            ...$this->viewData(),
            ...$this->getCustomViewData()
        ];

        if($this instanceof HasAssets) {
            moonshineAssets()->add($this->getAssets());
        }

        if(! is_null($this->onBeforeRenderCallback)) {
            value($this->onBeforeRenderCallback, $this);
        }

        return $this->view(
            $this->getView(),
            $this->data(),
            $mergeData,
        );
    }

    /**
     * @throws Throwable
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    public function escapeWhenCastingToString($escape = true): self
    {
        return $this;
    }
}
