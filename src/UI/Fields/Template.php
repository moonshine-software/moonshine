<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Traits\WithFields;

class Template extends Field implements HasFieldsContract
{
    use WithFields;

    protected ?Closure $renderCallback = null;

    protected function prepareFields(): FieldsContract
    {
        return tap(
            $this->getFields()->wrapNames($this->getColumn()),
            fn () => $this->getFields()
                ->onlyFields()
                ->map(fn (FieldContract $field): FieldContract => $field->setParent($this)->formName($this->getFormName()))
        );
    }

    protected function resolvePreview(): string|Renderable
    {
        return '';
    }

    protected function prepareFill(array $raw = [], ?CastedDataContract $casted = null): mixed
    {
        if($this->isFillChanged()) {
            return value(
                $this->fillCallback,
                is_null($casted) ? $raw : $casted->getOriginal(),
                $this
            );
        }

        return '';
    }

    /**
     * @param  Closure(mixed $value, static $ctx): string  $closure
     */
    public function changeRender(Closure $closure): static
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
