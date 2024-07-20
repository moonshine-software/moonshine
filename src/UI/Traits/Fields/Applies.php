<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\UI\FieldContract;

trait Applies
{
    protected ?Closure $canApply = null;

    protected ?Closure $onApply = null;

    protected ?Closure $onBeforeApply = null;

    protected ?Closure $onAfterApply = null;

    protected ?Closure $onAfterDestroy = null;

    public function canApply(Closure $canApply): static
    {
        $this->canApply = $canApply;

        return $this;
    }

    public function isCanApply(): bool
    {
        if(is_null($this->canApply)) {
            return true;
        }

        return value($this->canApply, $this);
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

        if (is_null($this->onApply)) {
            $classApply = $this->core
                ->getContainer(AppliesRegisterContract::class)
                ->findByField($this);

            $this->when(
                ! is_null($classApply),
                static fn (FieldContract $field): FieldContract => $field->onApply($classApply->apply($field))
            );
        }

        $applyFunction = is_null($this->onApply)
            ? $this->resolveOnApply()
            : $this->onApply;

        return is_null($applyFunction)
            ? $default($data, $this->getRequestValue(), $this)
            : $applyFunction($data, $this->getRequestValue(), $this);
    }

    public function beforeApply(mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        return is_null($this->onBeforeApply)
            ? $this->resolveBeforeApply($data)
            : value($this->onBeforeApply, $data, $this->getRequestValue(), $this);
    }

    public function afterApply(mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        return is_null($this->onAfterApply)
            ? $this->resolveAfterApply($data)
            : value($this->onAfterApply, $data, $this->getRequestValue(), $this);
    }

    public function afterDestroy(mixed $data): mixed
    {
        return is_null($this->onAfterDestroy)
            ? $this->resolveAfterDestroy($data)
            : value($this->onAfterDestroy, $data, $this->getRequestValue(), $this);
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): mixed  $onApply
     */
    public function onApply(Closure $onApply): static
    {
        $this->onApply = $onApply;

        return $this;
    }

    public function hasOnApply(): bool
    {
        return ! is_null($this->onApply);
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): static  $onBeforeApply
     */
    public function onBeforeApply(Closure $onBeforeApply): static
    {
        $this->onBeforeApply = $onBeforeApply;

        return $this;
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): static  $onAfterApply
     */
    public function onAfterApply(Closure $onAfterApply): static
    {
        $this->onAfterApply = $onAfterApply;

        return $this;
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): static  $onAfterDestroy
     */
    public function onAfterDestroy(Closure $onAfterDestroy): static
    {
        $this->onAfterDestroy = $onAfterDestroy;

        return $this;
    }
}
