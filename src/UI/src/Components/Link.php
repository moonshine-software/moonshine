<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Contracts\UI\WithBadgeContract;
use MoonShine\UI\Traits\WithBadge;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $href, Closure|string $label = '')
 */
final class Link extends MoonShineComponent implements HasIconContract, HasLabelContract, WithBadgeContract
{
    use WithBadge;
    use WithLabel;
    use WithIcon;

    protected bool $isButton = false;

    protected bool $isFilled = false;

    /**
     * @param (Closure(static): string)|string $href
     */
    public function __construct(
        protected Closure|string $href,
        Closure|string $label = '',
    ) {
        parent::__construct();

        $this->setLabel($label);
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

    public function blank(): self
    {
        return $this->customAttributes([
            'target' => '_blank',
        ]);
    }

    public function getView(): string
    {
        return 'moonshine::components.link-'
               . ($this->isButton ? 'button' : 'native');
    }

    protected function prepareBeforeRender(): void
    {
        $this->customAttributes([
            'href' => value($this->href, $this),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'slot' => new ComponentSlot(
                $this->getLabel() ?: value($this->href, $this),
            ),
            'icon' => new ComponentSlot(
                $this->getIcon(4),
            ),
            'filled' => $this->isFilled,
            'badge' => $this->hasBadge() ? $this->getBadge() : false,
        ];
    }
}
