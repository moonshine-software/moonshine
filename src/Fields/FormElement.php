<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\ShowWhen;
use MoonShine\Traits\Fields\WithFormElementAttributes;
use MoonShine\Traits\Fields\XModel;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithHint;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

/**
 * @method static static make(string|null $label = null, string|null $column = null, ?Closure $valueCallback = null)
 */
abstract class FormElement implements MoonShineRenderable, HasAssets
{
    use Makeable;
    use WithLabel;
    use WithFormElementAttributes;
    use WithComponentAttributes;
    use WithView;
    use WithHint;
    use WithAssets;
    use ShowWhen;
    use HasCanSee;
    use XModel;
    use Conditionable;

    protected string $column;

    protected ?FormElement $parent = null;

    protected bool $isGroup = false;

    protected ?Closure $valueCallback = null;

    protected ?string $requestKeyPrefix = null;

    protected bool $withWrapper = true;

    public function __construct(
        ?string $label = null,
        ?string $column = null,
        ?Closure $valueCallback = null
    ) {
        $this->setLabel(trim($label ?? (string) str($this->label)->ucfirst()));
        $this->setColumn(
            trim($column ?? (string) str($this->label)->lower()->snake())
        );

        if (! is_null($valueCallback)) {
            $this->setValueCallback($valueCallback);
        }
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            moonshineAssets()->add($this->getAssets());
        }
    }

    public function setColumn(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function column(): string
    {
        return $this->column;
    }

    protected function setValueCallback(Closure $valueCallback): void
    {
        $this->valueCallback = $valueCallback;
    }

    public function valueCallback(): ?Closure
    {
        return $this->valueCallback;
    }

    public function parent(): ?FormElement
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return ! is_null($this->parent);
    }

    public function setParents(): static
    {
        if ($this instanceof HasFields) {
            $fields = [];

            foreach ($this->getFields() as $field) {
                $field = $field->setParents();

                $fields[] = $field->setParent($this);
            }

            $this->fields($fields);
        }

        return $this;
    }

    protected function setParent(FormElement $field): static
    {
        $this->parent = $field;

        return $this;
    }

    protected function group(): static
    {
        $this->isGroup = true;

        return $this;
    }

    public function isGroup(): bool
    {
        return $this->isGroup;
    }

    public function withoutWrapper(mixed $condition = null): static
    {
        $this->withWrapper = Condition::boolean($condition, false);

        return $this;
    }

    public function hasWrapper(): bool
    {
        return $this->withWrapper;
    }

    public function setRequestKeyPrefix(?string $key): static
    {
        $this->requestKeyPrefix = $key;

        return $this;
    }

    public function requestValue(string|int|null $index = null): mixed
    {
        $nameDot = str(
            $this->isXModelField() ? $this->column() : $this->nameDot()
        )
            ->when(
                $this->requestKeyPrefix(),
                fn (Stringable $str): Stringable => $str->prepend(
                    "{$this->requestKeyPrefix()}."
                )
            )
            ->when(
                ! is_null($index) && $index !== '',
                fn (Stringable $str): Stringable => $str->append(".$index")
            )->value();

        $default = $this instanceof HasDefaultValue
            ? $this->getDefault()
            : false;

        return request($nameDot, $default) ?? false;
    }

    public function requestKeyPrefix(): ?string
    {
        return $this->requestKeyPrefix;
    }

    public function render(): View|Closure|string
    {
        if ($this instanceof Field && empty($this->getView())) {
            return $this->value();
        }

        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
