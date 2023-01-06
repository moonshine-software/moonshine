<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Exceptions\DecorationException;

class Tab extends Decoration
{
    /**
     * @throws DecorationException
     */
    public function getView(): string
    {
        throw new DecorationException('You need to use '.get_class(Tabs::class).' class');
    }
}
