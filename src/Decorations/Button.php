<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use MoonShine\Traits\Fields\WithLink;
use MoonShine\Traits\WithIcon;

/**
 * @method static static make(Closure|string $label, string $link, bool $blank = false)
 */
class Button extends Decoration
{
    use WithIcon;
    use WithLink;

    protected string $view = 'moonshine::decorations.button';

    public function __construct(
        Closure|string $label,
        string $link,
        bool $blank = false
    ) {
        parent::__construct($label);

        $this->addLink($this->label(), $link, $blank);
    }
}
