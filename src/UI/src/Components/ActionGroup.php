<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
use MoonShine\UI\Collections\ActionButtons;
use Throwable;

/**
 * @method static static make(iterable $actions = [])
 */
final class ActionGroup extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.action-group';

    public function __construct(iterable $actions = [])
    {
        parent::__construct($actions);
    }

    public function fill(?DataWrapperContract $data = null): self
    {
        $this->components = $this->getActions()->fill($data);

        return $this;
    }

    public function getActions(): ActionButtonsContract
    {
        return ActionButtons::make($this->components)->ensure(ActionButtonContract::class);
    }

    public function add(ActionButtonContract $item): self
    {
        $this->components = $this->getComponents();
        $this->components->add($item);

        return $this;
    }

    public function addMany(iterable $items): self
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    public function prepend(ActionButtonContract $item): self
    {
        $this->components = $this->getComponents();
        $this->components->prepend($item);

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

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
        ];
    }
}
