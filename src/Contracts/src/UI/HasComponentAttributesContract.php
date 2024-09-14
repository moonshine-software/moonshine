<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

interface HasComponentAttributesContract
{
    public function getAttributes(): ComponentAttributesBagContract;

    public function getAttribute(string $name, mixed $default = null): mixed;

    public function setAttribute(string $name, string|bool $value): static;

    public function removeAttribute(string $name): static;

    public function customAttributes(array $attributes, bool $override = false): static;

    public function iterableAttributes(int $level = 0): static;

    public function mergeAttribute(string $name, string $value, string $separator = ' '): static;

    public function class(string|array $classes): static;

    public function style(string|array $styles): static;
}
