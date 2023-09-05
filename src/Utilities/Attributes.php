<?php

declare(strict_types=1);

namespace MoonShine\Utilities;

use Illuminate\Support\Arr;
use ReflectionClass;

final class Attributes
{
    protected ?string $currentMethod = null;

    protected ?string $currentProperty = null;

    protected ?string $currentAttribute = null;

    protected ?string $currentAttributeProperty = null;

    public function __construct(protected object $class)
    {
    }

    public static function for(object $class): self
    {
        return new Attributes($class);
    }

    public function method(string $method): self
    {
        $this->currentMethod = $method;

        return $this;
    }

    public function property(string $property): self
    {
        $this->currentProperty = $property;

        return $this;
    }

    public function attribute(string $attribute): self
    {
        $this->currentAttribute = $attribute;

        return $this;
    }

    public function attributeProperty(string $property): self
    {
        $this->currentAttributeProperty = $property;

        return $this;
    }

    public function get(): mixed
    {
        $reflection = new ReflectionClass($this->class);

        if (! is_null($this->currentMethod)) {
            $reflection = $reflection->getMethod($this->currentMethod);
        }

        if (! is_null($this->currentProperty)) {
            $reflection = $reflection->getProperty($this->currentProperty);
        }

        if (! is_null($this->currentAttribute)) {
            /* @var ReflectionAttribute $attributes */
            $attributes = Arr::first($reflection->getAttributes($this->currentAttribute));
        } else {
            $attributes = $reflection->getAttributes();
        }

        if (! is_null($this->currentAttributeProperty)) {
            return $attributes?->newInstance()?->{$this->currentAttributeProperty};
        }

        return $attributes;
    }
}
