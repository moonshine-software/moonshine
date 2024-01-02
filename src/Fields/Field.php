<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;
use MoonShine\Components\Badge;
use MoonShine\Components\Url;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Support\Condition;
use MoonShine\Support\FieldEmptyValue;
use MoonShine\Traits\Fields\Applies;
use MoonShine\Traits\Fields\ShowOrHide;
use MoonShine\Traits\Fields\ShowWhen;
use MoonShine\Traits\Fields\WithBadge;
use MoonShine\Traits\Fields\WithLink;
use MoonShine\Traits\Fields\WithSorts;
use MoonShine\Traits\WithHint;
use MoonShine\Traits\WithIsNowOnRoute;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
 */
abstract class Field extends FormElement
{
    use Macroable;
    use WithLabel;
    use WithSorts;
    use WithHint;
    use ShowWhen;
    use ShowOrHide;
    use WithLink;
    use WithBadge;
    use WithIsNowOnRoute;
    use Applies;

    protected string $column;

    protected mixed $value = null;

    protected bool $rawMode = false;

    protected mixed $rawValue = null;

    protected bool $previewMode = false;

    protected bool $isForcePreview = false;

    protected ?Closure $previewCallback = null;

    protected ?Closure $fillCallback = null;

    protected mixed $formattedValue = null;

    protected ?Closure $formattedValueCallback = null;

    protected bool $nullable = false;

    protected array $attributes = ['type', 'disabled', 'required', 'readonly'];

    protected bool $isBeforeLabel = false;

    protected bool $isInLabel = false;

    protected bool $canBeEmpty = false;

    protected mixed $data = null;

    protected int $rowIndex = 0;

    public function __construct(
        Closure|string|null $label = null,
        ?string $column = null,
        ?Closure $formatted = null
    ) {
        $this->setLabel($label ?? $this->label());
        $this->setColumn(
            trim($column ?? str($this->label())->lower()->snake()->value())
        );

        if (! is_null($formatted)) {
            $this->setFormattedValueCallback($formatted);
        }
    }

    public function column(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): static
    {
        if ($this->showWhenState) {
            $this->showWhenCondition['showField'] = $column;
        }

        $this->column = $column;

        return $this;
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

        $default = $this->isCanBeEmpty() ? null : new FieldEmptyValue();

        $value = data_get($casted ?? $raw, $this->column(), $default);

        if (is_null($value) || $value === false || $value instanceof FieldEmptyValue) {
            $value = data_get($raw, $this->column(), $default);
        }

        return $value;
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $data;
    }

    public function resolveFill(array $raw = [], mixed $casted = null, int $index = 0): static
    {
        $this->setData($casted ?? $raw);
        $this->setRowIndex($index);

        $value = $this->prepareFill($raw, $casted);

        if($value instanceof FieldEmptyValue) {
            return $this;
        }

        $this->setRawValue($value);

        $value = $this->reformatFilledValue($value);

        $this->setValue($this->value ?? $value);

        return $this;
    }

    public function fill(mixed $value, mixed $casted = null): static
    {
        return $this->resolveFill([
            $this->column() => $value,
        ], [
            $this->column() => $casted ?? $value,
        ]);
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

    protected function setRawValue(mixed $value = null): static
    {
        $this->rawValue = $value;

        return $this;
    }

    protected function setData(mixed $data = null): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    protected function setRowIndex(int $index = 0): static
    {
        $this->rowIndex = $index;

        return $this;
    }

    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    public function toValue(bool $withDefault = true): mixed
    {
        $default = $withDefault && $this instanceof HasDefaultValue
            ? $this->getDefault()
            : null;

        return $this->isBlankValue() ? $default : $this->value;
    }

    protected function isBlankValue(): bool
    {
        return is_null($this->value);
    }

    public function value(bool $withOld = true): mixed
    {
        $this->previewMode = false;

        $old = old($this->nameDot());

        if ($withOld && $old) {
            return $old;
        }

        return $this->resolveValue();
    }

    public function setValue(mixed $value = null): static
    {
        $this->value = $value;

        return $this;
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue();
    }

    protected function setFormattedValue(mixed $value = null): static
    {
        $this->formattedValue = $value;

        return $this;
    }

    protected function setFormattedValueCallback(Closure $formattedValueCallback): void
    {
        $this->formattedValueCallback = $formattedValueCallback;
    }

    public function formattedValueCallback(): ?Closure
    {
        return $this->formattedValueCallback;
    }

    public function toFormattedValue(): mixed
    {
        if (is_closure($this->formattedValueCallback())) {
            $this->setFormattedValue(
                value(
                    $this->formattedValueCallback(),
                    $this->getData(),
                    $this->getRowIndex()
                )
            );
        }

        return $this->formattedValue ?? $this->toValue(withDefault: false);
    }

    public function changeFill(Closure $closure): static
    {
        $this->fillCallback = $closure;

        return $this;
    }

    public function isFillChanged(): bool
    {
        return ! is_null($this->fillCallback);
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

    public function isPreviewMode(): bool
    {
        return $this->isForcePreview() || $this->previewMode;
    }

    public function forcePreview(): static
    {
        $this->isForcePreview = true;

        return $this;
    }

    public function isForcePreview(): bool
    {
        return $this->isForcePreview;
    }

    public function preview(): View|string
    {
        $this->previewMode = true;

        if ($this->isPreviewChanged()) {
            return (string) value(
                $this->previewCallback,
                $this->toValue(),
                $this,
            );
        }

        $preview = $this->resolvePreview();

        if ($this->isRawMode()) {
            return $preview;
        }

        return $this->previewDecoration($preview);
    }

    protected function resolvePreview(): View|string
    {
        return (string) ($this->toFormattedValue() ?? '');
    }

    private function previewDecoration(View|string $value): View|string
    {
        if ($value instanceof View) {
            return $value->render();
        }

        if ($this->hasLink()) {
            $href = $this->getLinkValue($value);

            $value = (string) Url::make(
                href: $href,
                value: $this->getLinkName($value) ?: $value,
                icon: $this->getLinkIcon(),
                withoutIcon: $this->isWithoutIcon(),
                blank: $this->isLinkBlank()
            )->render();
        }

        if ($this->isBadge()) {
            return Badge::make($value, $this->badgeColor($this->toValue()))
                ->render();
        }

        return $value;
    }

    public function reset(): static
    {
        return $this
            ->setValue()
            ->setRawValue()
            ->setFormattedValue();
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

    protected function isCanBeEmpty(): bool
    {
        return $this->canBeEmpty;
    }

    public function inLabel(): static
    {
        $this->isInLabel = true;

        return $this;
    }

    public function isInLabel(): bool
    {
        return $this->isInLabel;
    }

    public function beforeLabel(): static
    {
        $this->isBeforeLabel = true;

        return $this;
    }

    public function isBeforeLabel(): bool
    {
        return $this->isBeforeLabel;
    }
}
