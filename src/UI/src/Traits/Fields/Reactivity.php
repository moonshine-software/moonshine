<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Contracts\RangeFieldContract;
use MoonShine\UI\Exceptions\FieldException;

/**
 * @mixin FieldContract
 */
trait Reactivity
{
    protected ?Closure $reactiveCallback = null;

    protected ?Closure $reactiveAttributes = null;

    protected bool $isReactive = false;

    public function isReactive(): bool
    {
        return $this->isReactive;
    }

    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): mixed
    {
        return $value;
    }

    public function isReactivitySupported(): bool
    {
        return true;
    }

    public function getReactiveCallback(FieldsContract $fields, mixed $value, array $values): FieldsContract
    {
        if (\is_null($this->reactiveCallback) || ! $this->isReactive()) {
            return $fields;
        }

        return \call_user_func($this->reactiveCallback, $fields, $value, $this, $values);
    }

    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
    ): static {
        if (! $this->isReactivitySupported()) {
            throw FieldException::reactivityNotSupported(static::class);
        }

        $this->isReactive = true;
        $this->reactiveCallback = $callback;

        $attribute = Str::of('x-model')
            ->when(
                $lazy,
                static fn (Stringable $str) => $str->append('.lazy'),
            )
            ->when(
                $debounce,
                static fn (Stringable $str) => $str->append(".debounce.{$debounce}ms"),
            )
            ->when(
                $throttle,
                static fn (Stringable $str) => $str->append(".throttle.{$throttle}ms"),
            )
            ->value();

        $this->wrapperClass("field-{$this->getColumn()}-wrapper");

        $this->reactiveAttributes = static fn (string $dot, string $class): array => [
            $attribute => "reactive.$dot",
            'class' => "field-$class-element",
            'data-column' => $dot,
            'data-reactive-column' => $dot,
        ];

        if ($this instanceof HasFieldsContract) {
            return $this;
        }

        if ($this instanceof RangeFieldContract) {
            return $this
                ->fromAttributes(
                    $this->getReactiveAttributes("{$this->getColumn()}.{$this->getFromField()}", "{$this->getColumn()}-{$this->getFromField()}"),
                )
                ->toAttributes(
                    $this->getReactiveAttributes("{$this->getColumn()}.{$this->getToField()}", "{$this->getColumn()}-{$this->getToField()}"),
                );
        }

        return $this->customAttributes($this->getReactiveAttributes($this->getColumn()));
    }

    /**
     * @return array<string, string>
     */
    public function getReactiveAttributes(?string $dot = null, ?string $class = null): array
    {
        if (! $this->isReactive()) {
            return [];
        }

        $dot ??= $this->getColumn();
        $class ??= str_replace('.', '-', $dot);

        return \call_user_func($this->reactiveAttributes, $dot, $class);
    }

    public function getReactiveValue(): mixed
    {
        return $this->getValue();
    }
}
