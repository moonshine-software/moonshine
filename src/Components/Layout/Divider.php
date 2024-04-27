<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Closure;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label = '', bool $centered = false)
 */
class Divider extends MoonShineComponent
{
    use WithLabel;

    protected string $view = 'moonshine::components.layout.divider';

    protected bool $isCentered = false;

    public function __construct(
        Closure|string $label = '',
        bool $centered = false
    )
    {
        parent::__construct();

        $this->isCentered = $centered;

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
