<?php


namespace Leeto\MoonShine\Traits\Fields;

trait XModelTrait
{
    public bool $xModel = false;

    public function xModel(): static
    {
        $this->xModel = true;

        return $this;
    }

    public function xModelField(string $variable = 'item'): string
    {
        return (string) str($variable)
            ->whenNotEmpty(fn($f) => $f->append('.'))
            ->append($this->field());
    }
}