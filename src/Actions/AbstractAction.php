<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithModal;
use MoonShine\Traits\WithOffCanvas;
use MoonShine\Traits\WithView;

abstract class AbstractAction implements MoonShineRenderable
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithIcon;
    use WithModal;
    use WithOffCanvas;
    use HasCanSee;
    use InDropdownOrLine;
    use WithComponentAttributes;
    use Conditionable;

    public function render(): View|Closure|string
    {
        return view(
            $this->getView() !== ''
                ? $this->getView()
                : 'moonshine::actions.default',
            [
                'action' => $this,
            ]
        );
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
