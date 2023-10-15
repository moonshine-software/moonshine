<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithView;

abstract class AbstractAction implements MoonShineRenderable
{
    use Makeable;
    use Macroable;
    use WithView;
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
