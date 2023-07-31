<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Helpers\Condition;

trait WithHtmlAttributes
{
    protected bool $required = false;

    protected bool $disabled = false;

    protected bool $readonly = false;

    protected bool $hidden = false;

    protected array $attributes = ['type', 'disabled', 'required', 'readonly'];

    protected array $customAttributes = [];

    public function getAttribute(string $name): mixed
    {
        return $this->attributes()->get($name);
    }

    public function attributes(): ComponentAttributeBag
    {
        $resolveAttributes = collect($this->attributes)->mapWithKeys(
            function ($attr) {
                $property = (string) str($attr)->camel();

                return isset($this->{$property}) ? [$attr => $this->{$property}]
                    : [];
            }
        );

        return (new ComponentAttributeBag(
            $this->customAttributes + $resolveAttributes->toArray()
        ));
    }

    public function removeAttribute(string $name): static
    {
        unset($this->customAttributes[$name]);
        $this->attributes = array_filter(
            $this->attributes,
            static fn ($attr): bool => $attr !== $name
        );

        return $this;
    }

    public function customAttributes(array $attributes): static
    {
        $this->customAttributes = array_merge(
            $this->customAttributes,
            $attributes
        );

        return $this;
    }

    public function setAttribute(string $name, string|bool $value): static
    {
        $this->attributes[] = $name;
        $this->customAttributes[$name] = $value;

        return $this;
    }

    public function isFile(): bool
    {
        return $this->type() === 'file';
    }

    public function type(): string
    {
        return $this->hidden
            ? 'hidden'
            : $this->attributes()->get('type', '');
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
