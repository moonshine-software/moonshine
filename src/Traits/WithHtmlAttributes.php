<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use Illuminate\View\ComponentAttributeBag;
use Leeto\MoonShine\Helpers\Condition;

trait WithHtmlAttributes
{
    protected ?string $name = null;

    protected ?string $id = null;

    protected bool $required = false;

    protected bool $disabled = false;

    protected bool $readonly = false;

    protected bool $hidden = false;

    protected array $attributes = ['type', 'disabled', 'required', 'readonly'];

    protected array $customAttributes = [];

    protected array $customClasses = [];

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
            ->when(! is_null($index), fn (Stringable $str) => $str->append("_$index"));
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

        return (string) str($this->field())
            ->when(! is_null($wrap), fn (Stringable $str) => $str->wrap("{$wrap}[", "]"))
            ->when(
                $this->isGroup() || $this->getAttribute('multiple'),
                fn (Stringable $str) => $str->append("[".($index ?? '')."]")
            );
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

    public function type(): string
    {
        return $this->hidden
            ? 'hidden'
            : $this->attributes()->get('type', '');
    }

    public function isFile(): bool
    {
        return $this->type() === 'file';
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes()->get($name);
    }

    public function setAttribute(string $name, string|bool $value): static
    {
        $this->attributes[] = $name;
        $this->customAttributes[$name] = $value;

        return $this;
    }

    public function removeAttribute(string $name): static
    {
        unset($this->customAttributes[$name]);
        $this->attributes = array_filter($this->attributes, static fn ($attr) => $attr !== $name);

        return $this;
    }

    public function attributes(): ComponentAttributeBag
    {
        $resolveAttributes = collect($this->attributes)->mapWithKeys(function ($attr) {
            $property = (string) str($attr)->camel();

            return isset($this->{$property}) ? [$attr => $this->{$property}] : [];
        });

        return (new ComponentAttributeBag(
            $this->customAttributes + $resolveAttributes->toArray()
        )
        )->class($this->customClasses);
    }

    public function customAttributes(array $attributes): static
    {
        $this->customAttributes = $attributes;

        return $this;
    }

    public function customClasses(array $classes): static
    {
        $this->customClasses = $classes;

        return $this;
    }

    public function required($condition = null): static
    {
        $this->required = Condition::boolean($condition, true);
        $this->setAttribute('required', $this->required);

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function disabled($condition = null): static
    {
        $this->disabled = Condition::boolean($condition, true);
        $this->setAttribute('disabled', $this->disabled);

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function hidden($condition = null): static
    {
        $this->hidden = Condition::boolean($condition, true);

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden || $this->attributes()->get('type') === 'hidden';
    }

    public function readonly($condition = null): static
    {
        $this->readonly = Condition::boolean($condition, true);
        $this->setAttribute('readonly', $this->readonly);

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }
}
