<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Core\Contracts\CastedData;
use MoonShine\UI\Collections\ActionButtons;

/**
 * @method static static make(array $actions = [])
 */
final class ActionGroup extends MoonShineComponent
{
    protected string $view = 'moonshine::components.action-group';

    public function __construct(protected array|ActionButtons $actions = [])
    {
        parent::__construct();
    }

    public function fill(?CastedData $data = null): self
    {
        $this->actions = $this->getActions()->fill($data);

        return $this;
    }

    public function getActions(): ActionButtons
    {
        return is_array($this->actions)
            ? ActionButtons::make($this->actions)
            : $this->actions;
    }

    public function add(ActionButton $item): self
    {
        $this->actions = $this->getActions();

        $this->actions->add($item);

        return $this;
    }

    public function addMany(iterable $items): self
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    public function prepend(ActionButton $item): self
    {
        $this->actions = $this->getActions();

        $this->actions->prepend($item);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'actions' => $this->getActions()->onlyVisible(),
        ];
    }
}
