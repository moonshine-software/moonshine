<?php

namespace MoonShine\Traits\Fields;

use Closure;

trait ActionEvents
{
    protected ?Closure $onApply = null;

    protected ?Closure $onBeforeApply = null;

    protected ?Closure $onAfterApply = null;

    protected ?Closure $onAfterDestroy = null;

    protected function resolveOnApply(): ?Closure
    {
        return $this->onApply;
    }

    protected function resolveBeforeApply(mixed $data): void
    {
        // Logic here
    }

    protected function resolveAfterApply(mixed $data): void
    {
        // Logic here
    }

    protected function resolveAfterDestroy(mixed $data): void
    {
        // Logic here
    }

    public function onApply(Closure $onApply): static
    {
        $this->onApply = $onApply;

        return $this;
    }

    public function apply(Closure $default, mixed $data): mixed
    {
        return is_callable($this->resolveOnApply())
            ? call_user_func($this->resolveOnApply(), $this, $data)
            : $default($this, $data);
    }

    public function beforeApply(mixed $data): void
    {
        is_callable($this->onBeforeApply)
            ? call_user_func($this->onBeforeApply, $this, $data)
            : $this->resolveBeforeApply($data);
    }

    public function afterApply(mixed $data): void
    {
        is_callable($this->onAfterApply)
            ? call_user_func($this->onAfterApply, $this, $data)
            : $this->resolveAfterApply($data);
    }

    public function afterDestroy(mixed $data): void
    {
        is_callable($this->onAfterDestroy)
            ? call_user_func($this->onAfterDestroy, $this, $data)
            : $this->resolveAfterDestroy($data);
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
