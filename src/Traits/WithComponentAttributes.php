<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

use Illuminate\View\ComponentAttributeBag;

trait WithComponentAttributes
{
    protected array $attributes = [];

    protected array $customAttributes = [];

    protected array $customClasses = [];

    protected function getAttribute(string $name): mixed
    {
        return $this->attributes()->get($name);
    }

    protected function setAttribute(string $name, string|bool $value): static
    {
        $this->attributes[] = $name;
        $this->customAttributes[$name] = $value;

        return $this;
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

    public function attributes(): ComponentAttributeBag
    {
        $resolveAttributes = collect($this->attributes)->mapWithKeys(function ($attr) {
            $property = (string) str($attr)->camel();

            return isset($this->{$property}) ? [$attr => $this->{$property}] : [];
        });

        return (new ComponentAttributeBag($resolveAttributes->toArray()))
            ->class($this->customClasses)
            ->merge($this->customAttributes);
    }
}
