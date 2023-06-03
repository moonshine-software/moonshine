<?php

declare(strict_types=1);

namespace MoonShine\IndexComponents;

use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithUniqueId;
use MoonShine\Traits\WithView;

/**
 * @method static static make(string $label)
 */
abstract class IndexComponent implements ResourceRenderable
{
    use Makeable;
    use HasCanSee;
    use WithView;
    use WithLabel;
    use WithUniqueId;

    final public function __construct(
        string $label
    ) {
        $this->setLabel($label);
    }
}
