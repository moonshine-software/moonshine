<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

use Closure;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use Throwable;

trait WithComponentAttributes
{
    /**
     * The component attributes.
     *
     * @var MoonShineComponentAttributeBag
     */
    public $attributes;

    protected array $withAttributes = [];

    public function getAttributes(): MoonShineComponentAttributeBag
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes->get($name, $default);
    }

    public function mergeAttribute(string $name, string $value, string $separator = ' '): static
    {
        $this->attributes->concat($name, $value, $separator);

        return $this;
    }

    public function class(string|array $classes): static
    {
        $this->attributes = $this->attributes->class($classes);

        return $this;
    }

    public function style(string|array $styles): static
    {
        $this->attributes = $this->attributes->style($styles);

        return $this;
    }

    public function setAttribute(string $name, string|bool $value): static
    {
        $this->attributes->set($name, $value);

        return $this;
    }

    public function removeAttribute(string $name): static
    {
        $this->attributes->remove($name);

        return $this;
    }

    public function customAttributes(array $attributes, bool $override = false): static
    {
        if ($override) {
            foreach (array_keys($attributes) as $name) {
                $this->removeAttribute($name);
            }
        }

        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function iterableAttributes(int $level = 0): static
    {
        if (! $this instanceof FieldContract) {
            return $this;
        }

        if ($level === 0 && $this->hasParent()) {
            $this->getParent()?->customAttributes([
                'data-top-level' => true,
            ]);
        }

        return $this->customAttributes([
            'data-name' => $this->getNameAttribute(),
            'data-column' => str($this->getColumn())->explode('.')->last(),
            'data-level' => $level,
        ]);
    }

    /** AlpineJs sugar methods */

    public function x(string $type, mixed $value = null): static
    {
        if (is_array($value)) {
            try {
                $value = str_replace('"', "'", json_encode($value, JSON_THROW_ON_ERROR));
            } catch (Throwable) {
                $value = null;
            }
        }

        return $this->customAttributes([
            "x-$type" => $value ?? true,
        ]);
    }

    public function xData(null|array|string $data = null): static
    {
        return $this->x('data', $data);
    }

    public function xDataMethod(string $method, ...$parameters): static
    {
        $data = [];

        foreach ($parameters as $parameter) {
            $data[] = str($parameter)->isJson() ? $parameter : "`$parameter`";
        }

        $data = implode(",", $data);

        return $this->x('data', "$method($data)");
    }

    public function xModel(?string $column = null): static
    {
        if ($this instanceof FieldContract) {
            return $this->x('model', $this->getColumn());
        }

        return $this->x('model', $column);
    }

    public function xShow(
        string|Closure $variable,
        ?string $operator = null,
        ?string $value = null,
        bool $wrapper = true
    ): static {
        return $this->xIfOrShow($variable, $operator, $value, wrapper: $wrapper);
    }

    public function xIf(
        string|Closure $variable,
        ?string $operator = null,
        ?string $value = null,
        bool $wrapper = true
    ): static {
        return $this->xIfOrShow($variable, $operator, $value, if: true, wrapper: $wrapper);
    }

    public function xDisplay(string $value, bool $html = true): static
    {
        return $this->x($html ? 'html' : 'text', $value);
    }

    private function xIfOrShow(
        string|Closure $variable,
        ?string $operator = null,
        ?string $value = null,
        bool $if = false,
        bool $wrapper = true
    ) {
        if ($if && ! $this instanceof FieldContract) {
            return $this;
        }

        if (! $variable instanceof Closure) {
            $o = is_null($value) ? '=' : $operator;
            $o = $o === '=' ? '==' : $o;
            $v = is_null($value) ? $operator : $value;
            $variable = static fn (self $ctx): string => "$variable$o'$v'";
        }

        $type = $if ? 'if' : 'show';

        if ($if) {
            return $this
                ->beforeRender(fn (): string => '<template x-if="' . $variable($this) . '">')
                ->afterRender(fn (): string => '</template>');
        }

        if ($this instanceof FieldContract && $wrapper) {
            return $this->customWrapperAttributes([
                "x-$type" => $variable($this),
            ]);
        }

        return $this->x($type, $variable($this));
    }
}
