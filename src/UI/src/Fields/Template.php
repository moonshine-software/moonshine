<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Traits\WithFields;

/**
 * @implements  HasFieldsContract<Fields|FieldsContract>
 */
class Template extends Field implements HasFieldsContract
{
    use WithFields;

    protected bool $hasOld = false;

    protected function prepareFields(): FieldsContract
    {
        return tap(
            $this->getFields()->wrapNames($this->getColumn()),
            fn ()
                => $this
                ->getFields()
                ->onlyFields()
                ->map(
                    fn (FieldContract $field): FieldContract
                        => $field
                        ->setParent($this)
                        ->formName($this->getFormName())
                        ->customAttributes($this->getReactiveAttributes("{$this->getColumn()}.{$field->getColumn()}")),
                ),
        );
    }

    protected function resolvePreview(): string|Renderable
    {
        return '';
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null): mixed
    {
        if ($this->isFillChanged()) {
            return \call_user_func(
                $this->fillCallback,
                \is_null($casted) ? $raw : $casted->getOriginal(),
                $this,
            );
        }

        return '';
    }

    public function getReactiveValue(): mixed
    {
        $value = $this->toValue();

        return filled($value) ? $value : $this
            ->getPreparedFields()
            ->onlyFields()
            ->mapWithKeys(fn (FieldContract $field): array => [$field->getColumn() => null]);
    }

    public function render(): string
    {
        if (\is_null($this->renderCallback)) {
            return '';
        }

        return (string)\call_user_func($this->renderCallback, $this->toValue(), $this);
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
