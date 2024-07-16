<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Fields\Field;

/** @mixin FieldContract */
trait Reactivity
{
    protected ?Closure $reactiveCallback = null;

    protected bool $isReactive = false;

    public function isReactive(): bool
    {
        return $this->isReactive;
    }

    public function getReactiveCallback(FieldsContract $fields, mixed $value, array $values): FieldsContract
    {
        if(is_null($this->reactiveCallback) || ! $this->isReactive()) {
            return $fields;
        }

        return value($this->reactiveCallback, $fields, $value, $this, $values);
    }

    /**
     * @param  ?Closure(Fields, mixed, array): Fields  $callback
     * @return $this
     */
    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
    ): static {
        $this->isReactive = true;
        $this->reactiveCallback = $callback;

        $attribute = str('x-model')
            ->when(
                $lazy,
                static fn (Stringable $str) => $str->append('.lazy')
            )
            ->when(
                $debounce,
                static fn (Stringable $str) => $str->append(".debounce.{$debounce}ms")
            )
            ->when(
                $throttle,
                static fn (Stringable $str) => $str->append(".throttle.{$throttle}ms")
            )
            ->value();

        return $this->customAttributes([
            $attribute => "reactive.{$this->getColumn()}",
            'class' => "field-{$this->getColumn()}-element",
            'data-column' => $this->getColumn(),
            'data-reactive-column' => $this->getColumn(),
        ])->customWrapperAttributes([
            'class' => "field-{$this->getColumn()}-wrapper",
        ]);
    }
}
