<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Traversable;

/**
 * @template-implements Traversable<array-key, ActionButtonContract>
 */
interface ActionButtonsContract extends Traversable
{
    public function fill(?DataWrapperContract $item): self;

    public function bulk(?string $forComponent = null): self;

    public function withoutBulk(): self;

    public function mergeIfNotExists(ActionButtonContract $new): self;

    public function onlyVisible(mixed $item = null): self;

    public function inLine(): self;

    public function inDropdown(): self;
}
