<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Traits\Fields\LinkTrait;
use MoonShine\Traits\WithIcon;

/**
 * @method static static make(string $label, string $link, bool $blank = false)
 */
class Button extends Decoration
{
    use WithIcon;
    use LinkTrait;

    protected string $view = 'moonshine::decorations.button';

    public function __construct(
        string $label,
        string $link,
        bool $blank = false
    ) {
        parent::__construct($label);

        $this->addLink($label, $link, $blank);
    }
}
