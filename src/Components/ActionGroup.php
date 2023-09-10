<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\ActionButtons\ActionButtons;

/**
 * @method static static make(array $actions = [])
 */
final class ActionGroup extends MoonshineComponent
{
    protected string $view = 'moonshine::components.action-group';

    protected $except = [
        'getActions',
    ];

    public function __construct(protected array|ActionButtons $actions = [])
    {
    }

    public function setItem(mixed $item): self
    {
        $this->getActions()->each(fn (ActionButton $button): ActionButton => $button->setItem($item));

        return $this;
    }

    public function getActions(): ActionButtons
    {
        return (is_array($this->actions)
            ? ActionButtons::make($this->actions)
            : $this->actions)->filter();
    }

    public function add(ActionButton $item): self
    {
        $this->actions = $this->getActions();

        $this->actions->add($item);

        return $this;
    }

    public function prepend(ActionButton $item): self
    {
        $this->actions = $this->getActions();

        $this->actions->prepend($item);

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'actions' => $this->getActions()->onlyVisible(),
        ];
    }
}
