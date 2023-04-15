<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

trait XModel
{
    public function clearXModel(): static
    {
        return $this
            ->removeAttribute('x-model-field')
            ->removeAttribute('x-model.lazy')
            ->removeAttribute('x-bind:name')
            ->removeAttribute('x-bind:id');
    }

    public function xModel(): static
    {
        return $this
            ->setAttribute('x-model-field', $this->xModelField())
            ->setAttribute('x-model.lazy', $this->xModelField())
            ->setAttribute('x-bind:name', "`{$this->name()}`")
            ->setAttribute('x-bind:id', "`{$this->id()}`");
    }

    public function xModelField(string $variable = 'item'): string
    {
        return (string) str($variable)
            ->whenNotEmpty(fn ($f) => $f->append('.'))
            ->append($this->field());
    }
}
