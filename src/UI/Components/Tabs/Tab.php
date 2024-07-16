<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Tabs;

use Closure;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Exceptions\MoonShineComponentException;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

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
    ) {
        if(is_iterable($labelOrComponents)) {
            /** @var iterable $labelOrComponents */
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
        throw MoonShineComponentException::tabsAreNotRendering();
    }

    public function active(Closure|bool|null $condition = null): self
    {
        $this->active = is_null($condition) || (value($condition, $this) ?? false);

        return $this;
    }

    public function getId(): string
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
