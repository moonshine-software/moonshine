<?php

declare(strict_types=1);

namespace MoonShine\Components\Tabs;

use Closure;
use MoonShine\Components\AbstractWithComponents;
use MoonShine\Exceptions\MoonShineComponentException;
use MoonShine\Support\Condition;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string|iterable $labelOrComponents = [], iterable $components = [])
 */
class Tab extends AbstractWithComponents
{
    use WithLabel;
    use WithIcon;

    public bool $active = false;

    public function __construct(
        Closure|string|iterable $labelOrComponents = [],
        iterable $components = [],
    )
    {
        if(is_iterable($labelOrComponents)) {
            $components = $labelOrComponents;
        } else {
            $this->setLabel($labelOrComponents);
        }

        parent::__construct($components);
    }

    /**
     * @throws MoonShineComponentException
     */
    public function getView(): string
    {
        throw new MoonShineComponentException(
            'You need to use ' . Tabs::class . ' class'
        );
    }

    public function active(Closure|bool|null $condition = null): self
    {
        $this->active = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function id(): string
    {
        return (string) spl_object_id($this);
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'label' => $this->getLabel(),
        ];
    }
}
