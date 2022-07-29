<?php


namespace Leeto\MoonShine\Traits\Fields;

trait XModel
{
    public function xModel(): static
    {
        return $this->setAttribute('x-model', $this->xModelField())
            ->setAttribute('x-bind:name', "`{$this->name()}`")
            ->setAttribute('x-bind:id', "`{$this->id()}`");
    }

    public function xModelField(string $variable = 'item'): string
    {
        return (string) str($variable)
            ->whenNotEmpty(fn($f) => $f->append('.'))
            ->append($this->field());
    }
}
