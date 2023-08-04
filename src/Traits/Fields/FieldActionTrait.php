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

    protected function resolveBeforeSave(mixed $data): void
    {
        // Logic here
    }

    protected function resolveAfterSave(mixed $data): void
    {
        // Logic here
    }

    protected function resolveAfterDelete(mixed $data): void
    {
        // Logic here
    }

    public function onSave(Closure $onSave): static
    {
        $this->onSave = $onSave;

        return $this;
    }

    public function save(Closure $default, mixed $data): mixed
    {
        return is_callable($this->resolveOnSave())
            ? call_user_func($this->resolveOnSave(), $this, $data)
            : $default($this, $data);
    }

    public function beforeSave(mixed $data): void
    {
        is_callable($this->onBeforeSave)
            ? call_user_func($this->onBeforeSave, $this, $data)
            : $this->resolveBeforeSave($data);
    }

    public function afterSave(mixed $data): void
    {
        is_callable($this->onAfterSave)
            ? call_user_func($this->onAfterSave, $this, $data)
            : $this->resolveAfterSave($data);
    }

    public function afterDelete(mixed $data): void
    {
        is_callable($this->onAfterDelete)
            ? call_user_func($this->onAfterDelete, $this, $data)
            : $this->resolveAfterDelete($data);
    }

    public function onBeforeSave(Closure $onBeforeSave): static
    {
        $this->onBeforeSave = $onBeforeSave;

        return $this;
    }

    public function onAfterSave(Closure $onAfterSave): static
    {
        $this->onAfterSave = $onAfterSave;

        return $this;
    }

    public function onAfterDelete(Closure $onAfterDelete): static
    {
        $this->onAfterDelete = $onAfterDelete;

        return $this;
    }
}
