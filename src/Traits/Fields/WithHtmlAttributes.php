<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Helpers\ConditionHelpers;

trait WithHtmlAttributes
{
    protected string|null $name = null;

    protected string|null $id = null;

    protected static string $type = '';

    protected string|null $default = null;

    protected bool $required = false;

    protected bool $disabled = false;

    protected bool $nullable = false;

    protected bool $multiple = false;

    protected bool $readonly = false;

    protected string $autocomplete = 'off';

    protected string $mask = '';

    protected array $options = [];

    public function id(string $index = null): string
    {
        if ($this->id) {
            return $this->id;
        }

        return (string)str($this->name ?? $this->name())
            ->replace(['[', ']'], '_')
            ->replaceMatches('/\${index\d+}/', '')
            ->replaceMatches('/_{2,}/', '_')
            ->trim('_')
            ->snake()
            ->when(!is_null($index), fn(Stringable $str) => $str->append("_$index"));
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index);
    }

    protected function prepareName($index = null, $wrap = null): string
    {
        if ($this->name) {
            return $this->name;
        }

        return (string)str($this->field())
            ->when(!is_null($wrap), fn(Stringable $str) => $str->wrap("{$wrap}[", "]"))
            ->when(
                $this->isMultiple(),
                fn(Stringable $str) => $str->append("[".($index ?? '')."]")
            );
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = str($id)->remove(['[', ']'])->snake();

        return $this;
    }

    public function type(): string
    {
        return static::$type;
    }

    public function attributes(): array
    {
        return get_object_vars($this);
    }

    public function getAttribute(string $name): mixed
    {
        return collect($this->attributes())->get($name);
    }

    public function required($condition = null): static
    {
        $this->required = ConditionHelpers::boolean($condition, true);

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function default(string $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getDefault(): string|null
    {
        return old($this->nameDot(), $this->default);
    }

    public function disabled($condition = null): static
    {
        $this->disabled = ConditionHelpers::boolean($condition, true);

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function mask(string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function hidden($condition = null): static
    {
        if (ConditionHelpers::boolean($condition, true)) {
            static::$type = 'hidden';
        }

        return $this;
    }

    public function isHidden(): bool
    {
        return static::$type === 'hidden';
    }

    public function readonly($condition = null): static
    {
        $this->readonly = ConditionHelpers::boolean($condition, true);

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function nullable($condition = null): static
    {
        $this->nullable = ConditionHelpers::boolean($condition, true);

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function multiple($condition = null): static
    {
        $this->multiple = ConditionHelpers::boolean($condition, true);

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function options(array $data): static
    {
        $this->options = $data;

        return $this;
    }

    public function relatedOptions(Model $item): array
    {
        $related = $item->{$this->relation()}()->getRelated();

        if (is_callable($this->resourceTitleCallback())) {
            $values = $related->all()
                ->mapWithKeys(function ($relatedItem) {
                    return [$relatedItem->getKey() => ($this->resourceTitleCallback())($relatedItem)];
                });
        } else {
            $values = $related->pluck($this->resourceTitleField(), $related->getKeyName());
        }

        return $values->toArray();
    }

    public function autocomplete(string $value): static
    {
        $this->autocomplete = $value;

        return $this;
    }

    public function getAutocomplete(): string
    {
        return $this->autocomplete;
    }

    public function values(): array
    {
        return $this->options;
    }

    public function isChecked(Model $item, string $value): bool
    {
        $formValue = $this->formViewValue($item);

        if ($formValue instanceof Collection) {
            return $this->formViewValue($item)->contains("id", "=", $value);
        }

        if (is_array($formValue)) {
            return in_array($value, $formValue);
        }

        return false;
    }

    public function isSelected(Model $item, string $value): bool
    {
        if (!$this->formViewValue($item)) {
            return false;
        }

        if ($this instanceof HasRelationshipContract
            && !$this->isRelationToOne() && !$this->isRelationHasOne()) {
            $related = $item->{$this->relation()}()->getRelated();


            return $this->formViewValue($item) instanceof Collection
                ? $this->formViewValue($item)->contains($related->getKeyName(), '=', $value)
                : in_array($value, $this->formViewValue($item));
        }

        return (string)$this->formViewValue($item) === $value
            || (!$this->formViewValue($item) && (string)$this->getDefault() === $value);
    }

    protected function nameDot(): string
    {
        $name = (string)str($this->name())->replace('[]', '');

        parse_str($name, $array);

        $result = collect(Arr::dot(array_filter($array)));


        return $result->isEmpty()
            ? $name
            : (string)str($result->keys()->first());
    }
}
