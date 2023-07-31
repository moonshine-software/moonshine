<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Actions\Actions;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\Makeable;

/**
 * @method static static make(array $actions = [])
 */
final class ActionGroup extends Component implements MoonShineRenderable
{
    use Makeable;
    use Macroable;
    use Conditionable;

    protected $except = [
        'getActions',
    ];

    public function __construct(protected array $actions = [])
    {
    }

    public function getActions(): Actions
    {
        return Actions::make($this->actions);
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.action-group', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'actions' => $this->getActions()
        ]);
    }

    public function __toString()
    {
        return (string) $this->render();
    }
}
