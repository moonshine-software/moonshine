<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

use Closure;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormElementContract;
use Throwable;

/**
 * @phpstan-ignore trait.unused
 */
trait WithComponentAttributes
{
    /**
     * The component attributes.
     *
     * @var ComponentAttributesBagContract
     */
    public $attributes;

    /**
     * @var array<string, mixed>
     */
    protected array $withAttributes = [];

    public function getAttributes(): ComponentAttributesBagContract
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->getAttributes()->get($name, $default);
    }

    public function mergeAttribute(string $name, string $value, string $separator = ' '): static
    {
        $this->getAttributes()->concat($name, $value, $separator);

        return $this;
    }

    public function removeClass(string $pattern): static
    {
        /** @var string $before */
        $before = $this->attributes->get('class', '');

        $this->getAttributes()->remove('class');

        $this->attributes = $this->attributes->class(
            trim((string) preg_replace("/(?<=\s|^)$pattern(?=\s|$)/", '', $before))
        );

        return $this;
    }

    /**
     * @param  string|array<int|string, bool|string>  $classes
     */
    public function class(string|array $classes): static
    {
        $this->attributes = $this->attributes->class($classes);

        return $this;
    }

    /**
     * @param  string|array<int|string, bool|string>  $styles
     */
    public function style(string|array $styles): static
    {
        $this->attributes = $this->attributes->style($styles);

        return $this;
    }

    public function setAttribute(string $name, string|bool $value): static
    {
        $this->getAttributes()->set($name, $value);

        return $this;
    }

    public function removeAttribute(string $name): static
    {
        $this->getAttributes()->remove($name);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
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
            /**
             * @var null|FormElementContract $parent
             */
            $parent = $this->getParent();

            $parent?->customAttributes([
                'data-top-level' => true,
            ]);
        }

        /** @var string $column */
        $column = $this->getColumn();

        /** @phpstan-ignore return.type */
        return $this->customAttributes([
            'data-name' => $this->getNameAttribute(),
            'data-column' => (string) Str::of($column)->explode('.')->last(),
            'data-level' => $level,
        ]);
    }

    /** AlpineJs sugar methods */

    public function x(string $type, mixed $value = null): static
    {
        if (\is_array($value)) {
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

    /**
     * @param  array<string, mixed>|string|null  $data
     */
    public function xData(null|array|string $data = null): static
    {
        return $this->x('data', $data);
    }

    /**
     * @param null|string ...$parameters
     */
    public function xDataMethod(string $method, ...$parameters): static
    {
        $data = [];

        foreach ($parameters as $parameter) {
            $data[] = Str::of($parameter ?? '')->isJson() ? $parameter : "`$parameter`";
        }

        $data = implode(",", $data);

        return $this->x('data', "$method($data)");
    }

    public function xModel(?string $column = null): static
    {
        if ($this instanceof FieldContract) {
            /** @var string $fieldColumn */
            $fieldColumn = $this->getColumn();

            /** @phpstan-ignore return.type */
            return $this->x('model', $column ?? $fieldColumn);
        }

        return $this->x('model', $column);
    }

    /**
     * @param  string|Closure(self): string  $variable
     */
    public function xShow(
        string|Closure $variable,
        ?string $operator = null,
        ?string $value = null,
        bool $wrapper = true,
    ): static {
        return $this->xIfOrShow($variable, $operator, $value, wrapper: $wrapper);
    }

    /**
     * @param  string|Closure(self): string  $variable
     */
    public function xIf(
        string|Closure $variable,
        ?string $operator = null,
        ?string $value = null,
        bool $wrapper = true,
    ): static {
        return $this->xIfOrShow($variable, $operator, $value, if: true, wrapper: $wrapper);
    }

    public function xDisplay(string $value, bool $html = true): static
    {
        return $this->x($html ? 'html' : 'text', $value);
    }

    /**
     * @param  string|Closure(self): string  $variable
     */
    private function xIfOrShow(
        string|Closure $variable,
        ?string $operator = null,
        ?string $value = null,
        bool $if = false,
        bool $wrapper = true,
    ): static {
        if ($if && ! $this instanceof FieldContract) {
            return $this;
        }

        if (! $variable instanceof Closure) {
            $o = \is_null($value) ? '=' : $operator;
            $o = $o === '=' ? '==' : $o;
            $v = \is_null($value) ? $operator : $value;
            $variable = static fn (self $ctx): string => "$variable$o'$v'";
        }

        $type = $if ? 'if' : 'show';

        if ($if && $this instanceof FieldContract) {
            /** @phpstan-ignore return.type,method.nonObject */
            return $this
                ->beforeRender(fn (): string => '<template x-if="' . $variable($this) . '">')
                ->afterRender(fn (): string => '</template>');
        }

        if ($this instanceof FieldContract && $wrapper) {
            /** @phpstan-ignore return.type */
            return $this->customWrapperAttributes([
                "x-$type" => $variable($this),
            ]);
        }

        return $this->x($type, $variable($this));
    }
}
