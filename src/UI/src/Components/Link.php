<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\UI\Traits\WithBadge;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $href, Closure|string $label = '')
 */
final class Link extends MoonShineComponent
{
    use WithBadge;
    use WithLabel;
    use WithIcon;

    protected bool $isButton = false;

    protected bool $isFilled = false;

    public function __construct(
        protected Closure|string $href,
        Closure|string $label = '',
    ) {
        parent::__construct();

        $this->setLabel($label);

        $this->customAttributes([
            'href' => $this->href,
        ]);
    }

    public function button(): self
    {
        $this->isButton = true;

        return $this;
    }

    public function filled(): self
    {
        $this->isFilled = true;

        return $this;
    }

    public function tooltip(?string $tooltip = null): self
    {
        $tooltip ??= $this->getLabel();

        $this->xDataMethod('tooltip', $tooltip);

        return $this;
    }

    public function getView(): string
    {
        return 'moonshine::components.link-'
            . ($this->isButton ? 'button' : 'native');
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'slot' => new ComponentSlot(
                $this->getLabel()
            ),
            'icon' => new ComponentSlot(
                $this->getIcon(4)
            ),
            'filled' => $this->isFilled,
            'badge' => $this->hasBadge() ? $this->getBadge() : false,
        ];
    }
}
