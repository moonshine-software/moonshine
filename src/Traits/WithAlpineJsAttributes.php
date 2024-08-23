<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\Fields\Field;
use Throwable;

/**
 * @mixin WithComponentAttributes
 */
trait WithAlpineJsAttributes
{
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
        if ($this instanceof Field) {
            return $this->x('model', $this->column());
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
        if($if && ! $this instanceof Field) {
            return $this;
        }

        if (! $variable instanceof Closure) {
            $o = is_null($value) ? '=' : $operator;
            $o = $o === '=' ? '==' : $o;
            $v = is_null($value) ? $operator : $value;
            $variable = static fn (self $ctx): string => "$variable$o'$v'";
        }

        $type = $if ? 'if' : 'show';

        if($if) {
            return $this
                ->beforeRender(fn (): string => '<template x-if="' . $variable($this) . '">')
                ->afterRender(fn (): string => '</template>');
        }

        if ($this instanceof Field && $wrapper) {
            return $this->customWrapperAttributes([
                "x-$type" => $variable($this),
            ]);
        }

        return $this->x($type, $variable($this));
    }
}
