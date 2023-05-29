<?php

declare(strict_types=1);

namespace MoonShine\DetailComponents;

use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

abstract class DetailComponent implements ResourceRenderable
{
    use Makeable;
    use HasCanSee;
    use WithView;
    use WithLabel;

    final public function __construct(
        string $label
    ) {
        $this->setLabel($label);
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug('_');
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }
}
