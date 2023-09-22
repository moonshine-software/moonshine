<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Support\Condition;
use MoonShine\Traits\Fields\WithFormElementAttributes;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithView;

abstract class FormElement implements MoonShineRenderable, HasAssets
{
    use Makeable;
    use Macroable;
    use WithFormElementAttributes;
    use WithComponentAttributes;
    use WithView;
    use WithAssets;
    use HasCanSee;
    use Conditionable;

    protected ?FormElement $parent = null;

    protected bool $isGroup = false;

    protected bool $withWrapper = true;

    protected ?string $requestKeyPrefix = null;

    protected ?string $formName = null;

    protected array $wrapperAttributes = [];

    protected ?Closure $beforeRender = null;

    protected ?Closure $afterRender = null;

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            moonshineAssets()->add($this->getAssets());
        }
    }

    public function parent(): ?FormElement
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return ! is_null($this->parent);
    }

    public function setParent(FormElement $field): static
    {
        $this->parent = $field;

        return $this;
    }

    protected function group(): static
    {
        $this->isGroup = true;

        return $this;
    }

    public function isGroup(): bool
    {
        return $this->isGroup;
    }

    public function withoutWrapper(mixed $condition = null): static
    {
        $this->withWrapper = Condition::boolean($condition, false);

        return $this;
    }

    public function hasWrapper(): bool
    {
        return $this->withWrapper;
    }

    public function customWrapperAttributes(array $attributes): self
    {
        $this->wrapperAttributes = array_merge($attributes, $this->wrapperAttributes);

        return $this;
    }

    public function wrapperAttributes(): ComponentAttributeBag
    {
        return new ComponentAttributeBag(
            $this->wrapperAttributes
        );
    }

    public function setRequestKeyPrefix(?string $key): static
    {
        $this->requestKeyPrefix = $key;

        return $this;
    }

    public function requestValue(string|int|null $index = null): mixed
    {
        $nameDot = str($this->nameDot())
            ->when(
                $this->requestKeyPrefix(),
                fn (Stringable $str): Stringable => $str->prepend(
                    "{$this->requestKeyPrefix()}."
                )
            )
            ->when(
                ! is_null($index) && $index !== '',
                fn (Stringable $str): Stringable => $str->append(".$index")
            )->value();

        $default = $this instanceof HasDefaultValue
            ? $this->getDefault()
            : false;

        return request($nameDot, $default) ?? false;
    }

    public function requestKeyPrefix(): ?string
    {
        return $this->requestKeyPrefix;
    }

    public function formName(string $formName): static
    {
        $this->formName = $formName;

        return $this;
    }

    public function getFormName(): ?string
    {
        return $this->formName;
    }

    public function beforeRender(Closure $closure): self
    {
        $this->beforeRender = $closure;

        return $this;
    }

    public function afterRender(Closure $closure): self
    {
        $this->afterRender = $closure;

        return $this;
    }

    public function getBeforeRender(): View|string
    {
        return is_null($this->beforeRender)
            ? ''
            : call_user_func($this->beforeRender, $this);
    }

    public function getAfterRender(): View|string
    {
        return is_null($this->afterRender)
            ? ''
            : call_user_func($this->afterRender, $this);
    }

    public function render(): View|Closure|string
    {
        if ($this instanceof Field && $this->getView() === '') {
            return $this->toValue();
        }

        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
