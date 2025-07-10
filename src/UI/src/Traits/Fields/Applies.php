<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormElementContract;

trait Applies
{
    protected static bool $silentApply = false;

    protected ?Closure $canApply = null;

    protected ?Closure $onApply = null;

    protected ?Closure $onBeforeApply = null;

    protected ?Closure $onAfterApply = null;

    protected ?Closure $onAfterDestroy = null;

    protected ?Closure $onRefreshAfterApply = null;

    /**
     * Using a field without side effects (for example, maintaining relationships)
     *
     * @template D of mixed
     * @param  Closure(): D  $callback
     *
     * @return D
     */
    public static function silentApply(Closure $callback): mixed
    {
        self::$silentApply = true;

        $data = $callback();

        self::$silentApply = false;

        return $data;
    }

    public function refreshAfterApply(?Closure $callback = null): static
    {
        $this->onRefreshAfterApply = $callback ?? static fn (FormElementContract $field): FormElementContract => $field;

        return $this;
    }

    public function disableRefreshAfterApply(): static
    {
        $this->onRefreshAfterApply = null;

        return $this;
    }

    public function isOnRefreshAfterApply(): bool
    {
        return $this->onRefreshAfterApply !== null;
    }

    public function resolveRefreshAfterApply(): static
    {
        if (! $this->isOnRefreshAfterApply()) {
            return $this;
        }

        return \call_user_func($this->onRefreshAfterApply, $this);
    }

    public function canApply(Closure $canApply): static
    {
        $this->canApply = $canApply;

        return $this;
    }

    public function isCanApply(): bool
    {
        if (\is_null($this->canApply)) {
            return true;
        }

        return (bool) \call_user_func($this->canApply, $this);
    }

    protected function resolveOnApply(): ?Closure
    {
        return $this->onApply;
    }

    protected function resolveBeforeApply(mixed $data): mixed
    {
        return $data;
    }

    protected function resolveAfterApply(mixed $data): mixed
    {
        return $data;
    }

    protected function resolveAfterDestroy(mixed $data): mixed
    {
        return $data;
    }

    /**
     * @param  ?class-string  $for
     */
    public function getApplyClass(string $type = 'fields', ?string $for = null): ?ApplyContract
    {
        if ($this instanceof FieldContract) {
            return $this->getCore()
                ->getContainer(AppliesRegisterContract::class)
                ->findByField($this, $type, $for);
        }

        return null;
    }

    /**
     * @template D
     * @param  Closure(D $data, mixed $value, static $ctx): D  $default
     * @param  D  $data
     *
     * @return D
     */
    public function apply(Closure $default, mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        if (\is_null($this->onApply) && ! $this->isConsoleMode()) {
            $classApply = $this->getApplyClass();

            $this->when(
                ! \is_null($classApply),
                static fn (FieldContract $field): FieldContract => $field->onApply($classApply->apply($field))
            );
        }

        $applyFunction = \is_null($this->onApply)
            ? $this->resolveOnApply()
            : $this->onApply;

        return \is_null($applyFunction)
            ? $default($data, $this->getRequestValue(), $this)
            : $applyFunction($data, $this->getRequestValue(), $this);
    }

    public function beforeApply(mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        return \is_null($this->onBeforeApply)
            ? $this->resolveBeforeApply($data)
            : \call_user_func($this->onBeforeApply, $data, $this->getRequestValue(), $this);
    }

    public function afterApply(mixed $data): mixed
    {
        if (! $this->isCanApply()) {
            return $data;
        }

        return \is_null($this->onAfterApply)
            ? $this->resolveAfterApply($data)
            : \call_user_func($this->onAfterApply, $data, $this->getRequestValue(), $this);
    }

    public function afterDestroy(mixed $data): mixed
    {
        return \is_null($this->onAfterDestroy)
            ? $this->resolveAfterDestroy($data)
            : \call_user_func($this->onAfterDestroy, $data, $this->getRequestValue(), $this);
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): mixed  $onApply
     */
    public function onApply(Closure $onApply): static
    {
        $this->onApply = $onApply;

        return $this;
    }

    public function hasOnApply(): bool
    {
        return ! \is_null($this->onApply);
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): static  $onBeforeApply
     */
    public function onBeforeApply(Closure $onBeforeApply): static
    {
        $this->onBeforeApply = $onBeforeApply;

        return $this;
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): static  $onAfterApply
     */
    public function onAfterApply(Closure $onAfterApply): static
    {
        $this->onAfterApply = $onAfterApply;

        return $this;
    }

    /**
     * @param  Closure(mixed, mixed, FieldContract): static  $onAfterDestroy
     */
    public function onAfterDestroy(Closure $onAfterDestroy): static
    {
        $this->onAfterDestroy = $onAfterDestroy;

        return $this;
    }
}
