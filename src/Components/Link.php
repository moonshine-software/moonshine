<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string $href, Closure|string $label = '')
 */
final class Link extends MoonShineComponent
{
    use WithLabel;
    use WithIcon;

    protected bool $isButton = false;

    protected bool $isFilled = false;

    public function __construct(
        protected Closure|string $href,
        Closure|string $label = '',
    ) {
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
        $tooltip ??= $this->label();

        $this->customAttributes([
            'x-data' => "tooltip(`$tooltip`)",
        ]);

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
                $this->label()
            ),
            'icon' => $this->iconValue(),
            'filled' => $this->isFilled,
        ];
    }
}
