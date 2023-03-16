<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Exceptions\DecorationException;
use Leeto\MoonShine\Traits\WithIcon;

class Tab extends Decoration
{
    use WithIcon;

    /**
     * @throws DecorationException
     */
    public function getView(): string
    {
        throw new DecorationException('You need to use '.Tabs::class.' class');
    }
}
