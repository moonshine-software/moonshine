<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\View\ComponentAttributeBag;

trait WithHtmlAttributes
{
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
}
