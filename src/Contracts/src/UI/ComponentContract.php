<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\Core\HasViewRendererContract;

/**
 * @mixin Conditionable
 */
interface ComponentContract extends
    HasCoreContract,
    HasComponentAttributesContract,
    HasViewRendererContract,
    HasCanSeeContract,
    HasAssetsContract
{
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
