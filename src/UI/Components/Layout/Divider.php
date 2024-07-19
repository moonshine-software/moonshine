<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label = '', bool $centered = false)
 */
class Divider extends MoonShineComponent
{
    use WithLabel;

    protected string $view = 'moonshine::components.layout.divider';

    public function __construct(
        Closure|string $label = '',
        protected bool $isCentered = false
    ) {
        parent::__construct();

        $this->setLabel($label);
    }

    public function centered(): self
    {
        $this->isCentered = true;

        return $this;
    }

    public function isCentered(): bool
    {
        return $this->isCentered;
    }

    protected function viewData(): array
    {
        return [
            'label' => $this->getLabel(),
            'centered' => $this->isCentered(),
        ];
    }
}
