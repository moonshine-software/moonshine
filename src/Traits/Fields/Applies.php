<?php

namespace MoonShine\Traits\Fields;

use Closure;
use MoonShine\Fields\Field;
use MoonShine\Support\Condition;

trait Applies
{
    protected bool $canApply = true;

    protected ?Closure $onApply = null;

    protected ?Closure $onBeforeApply = null;

    protected ?Closure $onAfterApply = null;

    protected ?Closure $onAfterDestroy = null;

    public function canApply(mixed $condition = null): static
    {
        $this->canApply = Condition::boolean($condition, true);

        return $this;
    }

    public function isCanApply(): bool
    {
        return $this->canApply;
    }

    protected function resolveOnApply(): ?Closure
    {
        return $this->onApply;
    }

    protected function resolveBeforeApply(mixed $data): mixed
    {
        return $data;
    }

    protected function resolveAfterApply(mixed $data): mixed
    {
        return $data;
    }

    protected function resolveAfterDestroy(mixed $data): mixed
    {
        return $data;
    }

    public function apply(Closure $default, mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        if (! is_closure($this->onApply)) {
            $classApply = findFieldApply(
                $this,
                'fields',
                'all',
            );

            $this->when(
                ! is_null($classApply),
                fn (Field $field): Field => $field->onApply($classApply->apply($field))
            );
        }

        $applyFunction = is_closure($this->onApply)
            ? $this->onApply
            : $this->resolveOnApply();

        return is_closure($applyFunction)
            ? $applyFunction($data, $this->requestValue(), $this)
            : $default($data, $this->requestValue());
    }

    public function beforeApply(mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        return is_closure($this->onBeforeApply)
            ? call_user_func($this->onBeforeApply, $data, $this->requestValue(), $this)
            : $this->resolveBeforeApply($data);
    }

    public function afterApply(mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        return is_closure($this->onAfterApply)
            ? call_user_func($this->onAfterApply, $data, $this->requestValue(), $this)
            : $this->resolveAfterApply($data);
    }

    public function afterDestroy(mixed $data): mixed
    {
        return is_closure($this->onAfterDestroy)
            ? call_user_func($this->onAfterDestroy, $data, $this->requestValue(), $this)
            : $this->resolveAfterDestroy($data);
    }

    /**
     * @param  Closure(mixed, mixed, Field): static  $onApply
     * @return $this
     */
    public function onApply(Closure $onApply): static
    {
        $this->onApply = $onApply;

        return $this;
    }

    public function onBeforeApply(Closure $onBeforeApply): static
    {
        $this->onBeforeApply = $onBeforeApply;

        return $this;
    }

    public function onAfterApply(Closure $onAfterApply): static
    {
        $this->onAfterApply = $onAfterApply;

        return $this;
    }

    public function onAfterDestroy(Closure $onAfterDestroy): static
    {
        $this->onAfterDestroy = $onAfterDestroy;

        return $this;
    }
}
