<?php

namespace MoonShine\Traits\Fields;

use Closure;

trait FieldActionTrait
{
    protected ?Closure $onSave = null;

    protected ?Closure $onBeforeSave = null;

    protected ?Closure $onAfterSave = null;

    protected ?Closure $onAfterDelete = null;

    protected function resolveOnSave(): ?Closure
    {
        return $this->onSave;
    }

    public function onSave(Closure $onSave): static
    {
        $this->onSave = $onSave;

        return $this;
    }

    public function save(Closure $default, mixed $item): mixed
    {
        return is_callable($this->resolveOnSave())
            ? call_user_func($this->resolveOnSave(), $this, $item)
            : $default($this, $item);
    }

    public function beforeSave(mixed $item): void
    {
        is_callable($this->onBeforeSave)
            ? call_user_func($this->onBeforeSave, $this, $item)
            : $this->resolveBeforeSave($item);
    }

    public function afterSave(mixed $item): void
    {
        is_callable($this->onAfterSave)
            ? call_user_func($this->onAfterSave, $this, $item)
            : $this->resolveAfterSave($item);
    }

    public function afterDelete(mixed $item): void
    {
        is_callable($this->onAfterDelete)
            ? call_user_func($this->onAfterDelete, $this, $item)
            : $this->resolveAfterDelete($item);
    }

    public function onBeforeSave(Closure $onBeforeSave): static
    {
        $this->onBeforeSave = $onBeforeSave;

        return $this;
    }

    public function onAfterSave(Closure $onAferSave): static
    {
        $this->onAfterSave = $onAferSave;

        return $this;
    }

    public function onAfterDelete(Closure $onAfterDelete): static
    {
        $this->onAfterDelete = $onAfterDelete;

        return $this;
    }

    protected function resolveBeforeSave(mixed $item): void
    {
    }

    protected function resolveAfterSave(mixed $item): void
    {
    }

    protected function resolveAfterDelete(mixed $item): void
    {
    }
}