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

    public function meta(): string
    {
        $meta = str('');

        if($this->xModel) {
            $meta = $meta->append(" x-model='{$this->xModelField()}' ");
            $meta = $meta->append(" x-bind:name=`{$this->name()}` ");
            $meta = $meta->append(" x-bind:id=`{$this->id()}` ");
        }

        return (string) $meta;
    }
}