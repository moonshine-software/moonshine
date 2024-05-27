<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use MoonShine\Core\Contracts\Fields\HasDefaultValue;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\Support\VO\FieldEmptyValue;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\Url;
use MoonShine\UI\Traits\Fields\Applies;
use MoonShine\UI\Traits\Fields\ShowWhen;
use MoonShine\UI\Traits\Fields\WithBadge;
use MoonShine\UI\Traits\Fields\WithHint;
use MoonShine\UI\Traits\Fields\WithLink;
use MoonShine\UI\Traits\Fields\WithSorts;

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
    use WithLink;
    use WithBadge;
    use Applies;

    protected string $column;

    protected mixed $value = null;

    protected bool $rawMode = false;

    protected mixed $rawValue = null;

    protected bool $previewMode = false;

    protected bool $isForcePreview = false;

    protected ?Closure $previewCallback = null;

    protected ?Closure $fillCallback = null;

    protected ?Closure $afterFillCallback = null;

    protected mixed $formattedValue = null;

    protected ?Closure $formattedValueCallback = null;

    protected bool $nullable = false;

    protected array $propertyAttributes = ['type'];

    protected bool $isBeforeLabel = false;

    protected bool $isInLabel = false;

    protected mixed $data = null;

    protected int $rowIndex = 0;

    protected bool $hasOld = true;

    public function __construct(
        Closure|string|null $label = null,
        ?string $column = null,
        ?Closure $formatted = null
    ) {
        parent::__construct();

        $this->attributes = new MoonShineComponentAttributeBag(
            $this->resolvePropertyAttributes()->toArray()
        );

        $this->setLabel($label ?? $this->getLabel());
        $this->setColumn(
            trim($column ?? str($this->getLabel())->lower()->snake()->value())
        );

        if (! is_null($formatted)) {
            $this->setFormattedValueCallback($formatted);
        }
    }

    protected function resolvePropertyAttributes(): Collection
    {
        return collect($this->propertyAttributes)->mapWithKeys(
            function ($attr): array {
                $property = (string) str($attr)->camel();

                return isset($this->{$property})
                    ? [$attr => $this->{$property}]
                    : [];
            }
        );
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): static
    {
        if ($this->showWhenState) {
            foreach (array_keys($this->showWhenCondition) as $key) {
                $this->showWhenCondition[$key]['showField'] = $column;
            }
        }

        $this->column = $column;

        return $this;
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        if ($this->isFillChanged()) {
            return value(
                $this->fillCallback,
                $casted ?? $raw,
                $this
            );
        }

        $default = new FieldEmptyValue();

        $value = data_get($casted ?? $raw, $this->getColumn(), $default);

        if (is_null($value) || $value === false || $value instanceof FieldEmptyValue) {
            $value = data_get($raw, $this->getColumn(), $default);
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

        if ($value instanceof FieldEmptyValue) {
            return $this;
        }

        $this->setRawValue($value);

        $value = $this->reformatFilledValue($value);

        $this->setValue($value);

        if (! is_null($this->afterFillCallback)) {
            return value($this->afterFillCallback, $this);
        }

        return $this;
    }

    public function fill(mixed $value, mixed $casted = null): static
    {
        return $this->resolveFill([
            $this->getColumn() => $value,
        ], [
            $this->getColumn() => $casted ?? $value,
        ]);
    }

    public function rawMode(Closure|bool|null $condition = null): static
    {
        $this->rawMode = value($condition, $this) ?? true;

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
        if (! $this->hasOld) {
            $withOld = false;
        }

        $empty = new FieldEmptyValue();
        $old = $withOld ? old($this->nameDot(), $empty) : $empty;

        if ($withOld && $old !== $empty) {
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
        if (! is_null($this->formattedValueCallback())) {
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

    public function afterFill(Closure $closure): static
    {
        $this->afterFillCallback = $closure;

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
            return Badge::make((string) $value, $this->badgeColor($this->toValue()))
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
        $this->nullable = value($condition, $this) ?? true;

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
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

    protected function prepareRender(View|Closure|string $view): View|Closure|string
    {
        if (! $this->isPreviewMode() && $this->hasWrapper()) {
            return (new FieldContainer(
                field: $this,
                slot: $view,
            ))->render();
        }

        return $view;
    }

    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'label' => $this->getLabel(),
            'column' => $this->getColumn(),
            'value' => $this->value(),
            'isNullable' => $this->isNullable(),
        ];
    }
}
