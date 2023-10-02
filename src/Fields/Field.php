<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Support\Condition;
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

    protected mixed $formattedValue = null;

    protected ?Closure $formattedValueCallback = null;

    protected bool $nullable = false;

    protected array $attributes = ['type', 'disabled', 'required', 'readonly'];

    protected bool $isBeforeLabel = false;

    protected bool $isInLabel = false;

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
        if($this->showWhenState) {
            $this->showWhenCondition['showField'] = $column;
        }

        $this->column = $column;

        return $this;
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        return data_get($casted ?? $raw, $this->column());
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $data;
    }

    public function resolveFill(array $raw = [], mixed $casted = null, int $index = 0): static
    {
        if ($this->value) {
            return $this;
        }

        $value = $this->prepareFill($raw, $casted);

        $this->setRawValue($value);

        $value = $this->reformatFilledValue($value);

        if (is_closure($this->formattedValueCallback())) {
            $this->setFormattedValue(
                call_user_func(
                    $this->formattedValueCallback(),
                    empty($casted) ? $this->toRawValue() : $casted,
                    $index
                )
            );
        }

        $this->setValue($value);

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

    public function toValue(bool $withDefault = true): mixed
    {
        $default = $withDefault && $this instanceof HasDefaultValue
            ? $this->getDefault()
            : null;

        return is_null($this->value) ? $default : $this->value;
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
        return $this->formattedValue ?? $this->toValue(withDefault: false);
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
        return $this->previewMode;
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
            return (string) call_user_func(
                $this->previewCallback,
                $this->toValue(),
                $this->toRawValue(),
            );
        }

        $preview = $this->resolvePreview();

        if($this->isRawMode()) {
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

            $value = view('moonshine::ui.url', [
                'value' => $this->getLinkName() ?? $value,
                'href' => $href,
                'blank' => $this->isLinkBlank(),
                'icon' => $this->getLinkIcon(),
                'withoutIcon' => $this->isWithoutIcon(),
            ])->render();
        }

        if ($this->isBadge()) {
            return view('moonshine::ui.badge', [
                'color' => $this->badgeColor($this->toValue()),
                'value' => $value,
            ])->render();
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
