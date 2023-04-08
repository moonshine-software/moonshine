<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Traits\Fields\LinkTrait;
use Leeto\MoonShine\Traits\WithIcon;

class Button extends Decoration
{
    use WithIcon;
    use LinkTrait;

    protected static string $view = 'moonshine::decorations.button';

    public function __construct(string $label, string $link, bool $blank = false)
    {
        parent::__construct($label);

        $this->addLink($label, $link, $blank);
    }
}
