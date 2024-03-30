<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Fields\Field;

// TODO array attributes to ?ComponentAttributeBag
trait WithComponentAttributes
{
    protected array $attributes = [];

    protected array $customAttributes = [];

    public function getAttribute(string $name): mixed
    {
        return $this->attributes()->get($name);
    }

    public function uniqueAttribute(string $old, string $new): string
    {
        return str($old)
            ->append(' ')
            ->append($new)
            ->lower()
            ->trim()
            ->explode(' ')
            ->unique()
            ->implode(' ');
    }

    public function mergeAttribute(string $name, string $value, string $separator = ' '): static
    {
        $this->attributes[$name] = $name;
        $previous = $this->customAttributes[$name] ?? '';

        if (str_contains($previous, $value)) {
            return $this;
        }

        if ($previous) {
            $value .= $separator . $previous;
        }

        $this->customAttributes[$name] = $value;

        return $this;
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
        $this->attributes = array_filter(
            $this->attributes,
            static fn ($attr): bool => $attr !== $name
        );

        return $this;
    }

    public function customAttributes(array $attributes): static
    {
        if (isset($attributes['class'])) {
            $this->customAttributes['class'] = $this->uniqueAttribute(
                old: $this->customAttributes['class'] ?? '',
                new: $attributes['class']
            );

            unset($attributes['class']);
        }

        $this->customAttributes = array_merge(
            $this->customAttributes,
            $attributes
        );

        return $this;
    }

    public function iterableAttributes(int $level = 0): static
    {
        if (! $this instanceof Field) {
            return $this;
        }

        return $this->customAttributes([
            'data-name' => $this->name(),
            'data-column' => str($this->column())->explode('.')->last(),
            'data-level' => $level,
        ]);
    }

    public function attributes(): ComponentAttributeBag
    {
        $resolveAttributes = collect($this->attributes)->mapWithKeys(
            function ($attr): array {
                $property = (string) str($attr)->camel();

                return isset($this->{$property}) ? [$attr => $this->{$property}]
                    : [];
            }
        );

        return (new ComponentAttributeBag(
            $this->customAttributes + $resolveAttributes->toArray()
        ));
    }
}
