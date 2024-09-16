<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI\Collection;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;

/**
 * @template-extends Enumerable<array-key, ActionButtonContract>
 *
 * @mixin Collection
 */
interface ActionButtonsContract extends Enumerable
{
    public function fill(?DataWrapperContract $item): self;

    public function bulk(?string $forComponent = null): self;

    public function withoutBulk(): self;

    public function mergeIfNotExists(ActionButtonContract $new): self;

    public function onlyVisible(): self;

    public function inLine(): self;

    public function inDropdown(): self;
}
