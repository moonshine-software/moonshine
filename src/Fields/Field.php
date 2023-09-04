<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\Applies;
use MoonShine\Traits\Fields\ShowOrHide;
use MoonShine\Traits\Fields\ShowWhen;
use MoonShine\Traits\Fields\WithBadge;
use MoonShine\Traits\Fields\WithLink;
use MoonShine\Traits\WithHint;
use MoonShine\Traits\WithIsNowOnRoute;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string|null $label = null, ?string $column = null, ?Closure $formattedValueCallback = null)
 */
abstract class Field extends FormElement
{
    use WithLabel;
    use WithHint;
    use ShowWhen;
    use ShowOrHide;
    use WithLink;
    use WithBadge;
    use WithIsNowOnRoute;
    use Applies;

    protected string $column;
    protected bool $rawMode = false;

    protected mixed $rawValue = null;

    protected mixed $value = null;
    protected mixed $formattedValue = null;

    protected ?Closure $formattedValueCallback = null;

    protected ?Closure $previewCallback = null;

    protected bool $sortable = false;

    protected bool $nullable = false;

    protected array $attributes = ['type', 'disabled', 'required', 'readonly'];

    public function __construct(
        Closure|string|null $label = null,
        ?string $column = null,
        ?Closure $formattedValueCallback = null
    ) {
        $this->setLabel($label ?? $this->label());
        $this->setColumn(
            trim($column ?? str($this->label())->lower()->snake()->value())
        );

        if (! is_null($formattedValueCallback)) {
            $this->setFormattedValueCallback($formattedValueCallback);
        }
    }

    public function column(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): static
    {
        $this->column = $column;

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

    public function sortQuery(string $url): string
    {
        $sortData = [
            'sort' => [
                'column' => $this->column(),
                'direction' => $this->sortActive() && $this->sortDirection('asc') ? 'desc'
                    : 'asc',
            ],
            'page' =>  request('page', 1)
        ];

        if(empty($url)) {
            return request()->fullUrlWithQuery($sortData);
        }

        return $url . '?' . Arr::query($sortData);
    }

    public function sortActive(): bool
    {
        return request('sort.column') === $this->column();
    }

    public function sortDirection(string $type): bool
    {
        return request('sort.direction') === strtolower($type);
    }

    public function setValue(mixed $value = null): self
    {
        $this->value = $value;

        return $this;
    }

    protected function setRawValue(mixed $value = null): self
    {
        $this->rawValue = $value;

        return $this;
    }

    protected function setFormattedValue(mixed $value = null): self
    {
        $this->formattedValue = $value;

        return $this;
    }

    public function reset(): self
    {
        return $this
            ->setValue()
            ->setRawValue()
            ->setFormattedValue();
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        return data_get($casted ?? $raw, $this->column());
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        return $data;
    }

    public function resolveFill(array $raw = [], mixed $casted = null, int $index = 0): self
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

    public function toValue(bool $withDefault = true): mixed
    {
        $default = $withDefault && $this instanceof HasDefaultValue
            ? $this->getDefault()
            : null;

        return $this->value ?? $default;
    }

    public function value(bool $withOld = true): mixed
    {
        $old = old($this->nameDot());

        if ($withOld && $old) {
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

    public function preview(): View|string
    {
        if ($this->isPreviewChanged()) {
            return (string) call_user_func(
                $this->previewCallback,
                $this->toValue(),
                $this->toRawValue(),
            );
        }

        $preview = $this->resolvePreview();

        return $this->previewDecoration($preview);
    }

    private function previewDecoration(View|string $value): View|string
    {
        if($value instanceof View) {
            return  $value;
        }

        if ($this->hasLink()) {
            $href = $this->getLinkValue($value);

            $value = view('moonshine::ui.url', [
                'value' => $value,
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

    protected function resolvePreview(): View|string
    {
        return (string) ($this->toFormattedValue() ?? '');
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
