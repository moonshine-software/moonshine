<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\Core\HasViewRendererContract;

interface ComponentContract extends
    HasCoreContract,
    HasComponentAttributesContract,
    HasViewRendererContract,
    HasCanSeeContract,
    HasAssetsContract
{
    /**
     * @template TValue
     * @template TReturn
     * @param (Closure($this): TValue)|TValue|null $value
     * @param (callable($this, TValue): TReturn)|null $callback
     * @param (callable($this, TValue): TReturn)|null $default
     * @return $this|TReturn
     */
    public function when($value = null, ?callable $callback = null, ?callable $default = null);

    /**
     * @template TValue
     * @template TReturn
     * @param (Closure($this): TValue)|TValue|null $value
     * @param (callable($this, TValue): TReturn)|null $callback
     * @param (callable($this, TValue): TReturn)|null $default
     * @return $this|TReturn
     */
    public function unless($value = null, ?callable $callback = null, ?callable $default = null);

    public function name(string $name): static;

    public function getName(): string;

    /**
     * @param  array<string, mixed>  $attributes
     *
     */
    public function withAttributes(array $attributes): static;

    /**
     * @return   array<string, mixed>
     *
     */
    public function data(): array;
}
