<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\ShowWhen;
use MoonShine\Traits\Fields\WithRelatedValues;
use MoonShine\Traits\Fields\WithResourceMode;
use MoonShine\Traits\Fields\XModel;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithHint;
use MoonShine\Traits\WithHtmlAttributes;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;
use MoonShine\Utilities\AssetManager;

/**
 * @method static static make(string|null $label = null, string|null $column = null, ?Closure $valueCallback = null)
 *
 * @mixin WithResourceMode
 * @mixin WithRelatedValues
 */
abstract class FormElement implements MoonShineRenderable, HasAssets
{
    use Makeable;
    use WithLabel;
    use WithHtmlAttributes;
    use WithView;
    use WithHint;
    use WithAssets;
    use ShowWhen;
    use HasCanSee;
    use XModel;
    use Conditionable;

    protected string $column;

    protected ?Field $parent = null;
    protected bool $group = false;

    protected ?Closure $valueCallback = null;

    protected bool $nullable = false;

    protected bool $fieldContainer = true;

    protected ?string $parentRequestValueKey = null;

    protected ?string $name = null;

    protected ?string $wrapName = null;

    protected ?string $id = null;

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

    public function nullable(Closure|bool|null $condition = null): static
    {
        $this->nullable = Condition::boolean($condition, true);

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function parent(): ?Field
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return $this->parent instanceof self;
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

    protected function setParent(Field $field): static
    {
        $this->parent = $field;

        return $this;
    }

    public function id(string $index = null): string
    {
        if ($this->id) {
            return $this->id;
        }

        return (string) str($this->name ?? $this->name())
            ->replace(['[', ']'], '_')
            ->replaceMatches('/\${index\d+}/', '')
            ->replaceMatches('/_{2,}/', '_')
            ->trim('_')
            ->snake()
            ->when(
                ! is_null($index),
                fn (Stringable $str): Stringable => $str->append("_$index")
            );
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index);
    }

    public function wrapName(string $wrapName): static
    {
        $this->wrapName = $wrapName;

        return $this;
    }

    protected function prepareName($index = null, $wrap = null): string
    {
        $wrap = $wrap ?? $this->wrapName;

        if ($this->name) {
            return $this->name;
        }

        return (string) str($this->column())
            ->when(
                ! is_null($wrap),
                fn (Stringable $str): Stringable => $str->wrap("{$wrap}[", "]")
            )
            ->when(
                $this->isGroup() || $this->getAttribute('multiple'),
                fn (Stringable $str): Stringable => $str->append(
                    "[" . ($index ?? '') . "]"
                )
            );
    }

    public function isGroup(): bool
    {
        return $this->group;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = (string) str($id)->remove(['[', ']'])->snake();

        return $this;
    }

    public function fieldContainer(mixed $condition = null): static
    {
        $this->fieldContainer = Condition::boolean($condition, true);

        return $this;
    }

    public function hasFieldContainer(): bool
    {
        return $this->fieldContainer;
    }

    public function setParentRequestValueKey(?string $key): static
    {
        $this->parentRequestValueKey = $key;

        return $this;
    }

    public function requestValue(string|int|null $index = null): mixed
    {
        $nameDot = str(
            $this->isXModelField() ? $this->column() : $this->nameDot()
        )
            ->when(
                $this->parentRequestValueKey(),
                fn (Stringable $str): Stringable => $str->prepend(
                    "{$this->parentRequestValueKey()}."
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

    protected function nameDot(): string
    {
        $name = (string) str($this->name())->replace('[]', '');

        parse_str($name, $array);

        $result = collect(Arr::dot(array_filter($array)));

        return $result->isEmpty()
            ? $name
            : (string) str($result->keys()->first());
    }

    public function parentRequestValueKey(): ?string
    {
        return $this->parentRequestValueKey;
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            app(AssetManager::class)->add($this->getAssets());
        }
    }

    protected function group(): static
    {
        $this->group = true;

        return $this;
    }

    public function render(): View|Closure|string
    {
        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
