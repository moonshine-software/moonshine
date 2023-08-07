<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Actions\Actions;
use MoonShine\Traits\Makeable;

/**
 * @method static make(array $actions = [])
 */
final class ActionGroup extends MoonshineComponent
{
    use Makeable;
    use Macroable;
    use Conditionable;

    protected $except = [
        'getActions',
    ];

    public function __construct(protected array|ActionButtons $actions = [])
    {
    }

    public function getActions(): Actions
    {
        return is_array($this->actions)
            ? Actions::make($this->actions)
            : $this->actions;
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.action-group', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'actions' => $this->getActions(),
        ]);
    }
}
