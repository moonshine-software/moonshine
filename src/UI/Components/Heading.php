<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\UI\Traits\Components\HasDifferentHtmlTag;
use MoonShine\UI\Traits\Components\WithHeadingGradation;

/**
 * @method static static make(Closure|string $label)
 */
class Heading extends MoonShineComponent
{
    use HasDifferentHtmlTag;
    use WithHeadingGradation;
    use WithLabel;

    protected string $view = 'moonshine::components.heading';

    public function __construct(Closure|string $label)
    {
        parent::__construct();

        $this->setLabel($label);
    }

    protected function viewData(): array
    {
        return [
            'tag' => $this->getTag(),
            'label' => $this->getLabel(),
        ];
    }
}
