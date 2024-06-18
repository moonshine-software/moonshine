<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Traits\WithFields;

class Template extends Field implements HasFields
{
    use WithFields;

    protected ?Closure $renderCallback = null;

    public function getPreparedFields(): FieldsCollection
    {
        return tap(
            $this->getFields()->wrapNames($this->getColumn()),
            fn () => $this->getFields()
                ->onlyFields()
                ->map(fn (Field $field): Field => $field->setParent($this)->formName($this->getFormName()))
        );
    }

    protected function resolvePreview(): string|View
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
