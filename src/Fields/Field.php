<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\FieldActionTrait;
use MoonShine\Traits\Fields\LinkTrait;
use MoonShine\Traits\Fields\ShowOrHide;
use MoonShine\Traits\WithIsNowOnRoute;

abstract class Field extends FormElement
{
    use ShowOrHide;
    use LinkTrait;
    use WithIsNowOnRoute;
    use FieldActionTrait;

    protected bool $rawMode = false;

    protected mixed $rawValue = null;

    protected mixed $value = null;

    protected mixed $formattedValue = null;

    protected ?Closure $previewCallback = null;

    protected bool $sortable = false;

    protected bool $canSave = true;

    protected bool $nullable = false;

    protected array $attributes = ['type', 'disabled', 'required', 'readonly'];

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

    public function setValue(mixed $value = null): self
    {
        $this->value = $value;

        return $this;
    }

    protected function setRawValue(mixed $value = null): void
    {
        $this->rawValue = $value;
    }

    protected function setFormattedValue(mixed $value = null): void
    {
        $this->formattedValue = $value;
    }

    public function resolveFill(array $rawValues = [], mixed $castedValues = null): self
    {
        if ($this->value) {
            return $this;
        }

        $value = $rawValues[$this->column()] ?? null;

        $this->setRawValue($value);

        if (is_callable($this->valueCallback())) {
            $this->setFormattedValue(
                call_user_func(
                    $this->valueCallback(),
                    !empty($castedValues) ? $castedValues : $this->toRawValue()
                )
            );
        }

        $this->setValue($value);

        return $this;
    }

    public function rawMode(Closure|bool|null $condition = null): static
    {
        $this->rawMode = Condition::boolean($condition, true);

        return $this;
    }

    public function isRawMode(): bool
    {
        return $this->rawMode;
    }

    public function toRawValue(): mixed
    {
        return $this->rawValue;
    }

    public function toValue(bool $withDefault = true): mixed
    {
        $default = $withDefault && $this instanceof HasDefaultValue
            ? $this->getDefault()
            : null;

        return $this->value ?? $default;
    }

    public function value(): mixed
    {
        $old = old($this->nameDot());

        if ($old) {
            return $old;
        }

        return $this->resolveValue();
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue();
    }

    public function toFormattedValue(): mixed
    {
        return $this->formattedValue ?? $this->toValue();
    }

    public function changePreview(Closure $closure): static
    {
        $this->previewCallback = $closure;

        return $this;
    }

    public function isPreviewChanged(): bool
    {
        return ! is_null($this->previewCallback);
    }

    public function preview(): string
    {
        if ($this->isPreviewChanged()) {
            return (string) call_user_func(
                $this->previewCallback,
                $this->toValue(),
                $this->toRawValue(),
            );
        }

        return $this->resolvePreview();
    }

    protected function resolvePreview(): string
    {
        return (string) ($this->toFormattedValue() ?? '');
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

    public function nullable(Closure|bool|null $condition = null): static
    {
        $this->nullable = Condition::boolean($condition, true);

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
