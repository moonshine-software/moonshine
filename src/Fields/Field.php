<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\LinkTrait;
use MoonShine\Traits\Fields\ShowOrHide;
use MoonShine\Traits\WithIsNowOnRoute;

abstract class Field extends FormElement
{
    use ShowOrHide;
    use LinkTrait;
    use WithIsNowOnRoute;

    protected mixed $rawValue = null;

    protected mixed $value = null;

    protected bool $sortable = false;

    protected bool $canSave = true;

    public function setValue(mixed $value = null)
    {
        $this->value = $value;
    }

    public function setRawValue(mixed $value = null)
    {
        $this->rawValue = $value;
    }

    public function resolveValue(array $rawValues = [], mixed $castedValues = null): self
    {
        $value = $rawValues[$this->column()] ?? null;

        $this->setRawValue($value);

        if (is_callable($this->valueCallback())) {
            $value = call_user_func(
                $this->valueCallback(),
                ! empty($castedValues) ? $castedValues : $rawValues
            );
        }

        $this->setValue($value);

        return $this;
    }

    /**
     * Define whether if index page can be sorted by this field
     *
     * @return $this
     */
    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function sortQuery(): string
    {
        return request()->fullUrlWithQuery([
            'order' => [
                'field' => $this->column(),
                'type' => $this->sortActive() && $this->sortType('asc') ? 'desc'
                    : 'asc',
            ],
        ]);
    }

    public function sortActive(): bool
    {
        return request()->has('order.field')
            && request('order.field') === $this->column();
    }

    public function sortType(string $type): bool
    {
        return request()->has('order.type')
            && request('order.type') === strtolower($type);
    }

    public function toRawValue(): mixed
    {
        return $this->rawValue;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function value(): mixed
    {
        $old = old($this->nameDot());

        if ($old) {
            return $old;
        }

        $default = $this instanceof HasDefaultValue
            ? $this->getDefault()
            : null;

        return $this->getValue() ?? $default;
    }

    public function preview(): string
    {
        return (string) ($this->value() ?? '');
    }

    public function canSave(mixed $condition = null): static
    {
        $this->canSave = Condition::boolean($condition, true);

        return $this;
    }

    public function isCanSave(): bool
    {
        return $this->canSave;
    }

    public function beforeSave(): void
    {
        //
    }

    public function afterSave(): void
    {
        //
    }

    public function afterDelete(): void
    {
        //
    }
}
