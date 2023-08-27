<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\ActionButtons\ActionButtons;

/**
 * @method static static make(array $actions = [])
 */
final class ActionGroup extends MoonshineComponent
{
    protected $except = [
        'getActions',
    ];

    public function __construct(protected array|ActionButtons $actions = [])
    {
    }

    public function getActions(): ActionButtons
    {
        return is_array($this->actions)
            ? ActionButtons::make($this->actions)
            : $this->actions;
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.action-group', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'actions' => $this->getActions()->onlyVisible(),
        ]);
    }
}
