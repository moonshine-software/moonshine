<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\WithFields;

final class Template extends Field implements HasFields
{
    use WithFields;

    protected ?Closure $renderCallback = null;

    public function preparedFields(): Fields
    {
        return tap(
            $this->getFields()->wrapNames($this->column()),
            fn() => $this->getFields()
                ->onlyFields()
                ->map(fn(Field $field) => $field->setParent($this))
        );
    }

    protected function resolvePreview(): string
    {
        return '';
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        if($this->isFillChanged()) {
            return value(
                $this->fillCallback,
                $casted ?? $raw,
                $this
            );
        }

        return '';
    }

    public function changeRender(Closure $closure): self
    {
        $this->renderCallback = $closure;

        return $this;
    }

    public function render(): string
    {
        return (string) value($this->renderCallback, $this->toValue(), $this);
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
