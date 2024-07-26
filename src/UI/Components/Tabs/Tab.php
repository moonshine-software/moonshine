<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Tabs;

use Closure;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Components\Components;
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

    private MoonShineComponentAttributeBag $labelAttributes;

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

        $this->labelAttributes = new MoonShineComponentAttributeBag();

        parent::__construct($components);

        $this->labelAttributes([
            '@click.prevent' => "setActiveTab(`{$this->getId()}`)",
            ':class' => "{ '_is-active': activeTab === '{$this->getId()}' }",
            'class' => "tabs-button",
        ]);

        $this->customAttributes([
            '@set-active-tab' => "setActiveTab(`{$this->getId()}`)",
            ':class' => "activeTab === '{$this->getId()}' ? 'block' : 'hidden'",
            'class' => "tab-panel",
        ]);
    }

    public function labelAttributes(array $attributes): static
    {
        $this->labelAttributes = $this->labelAttributes->merge($attributes);

        return $this;
    }

    /**
     * @throws MoonShineComponentException
     */
    public function getView(): string
    {
        throw MoonShineComponentException::tabsAreNotRendering();
    }

    public function active(Closure|bool|null $condition = null): static
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
            'icon' => $this->getIcon(6, Color::SECONDARY),
            'label' => $this->getLabel(),
            'labelAttributes' => $this->labelAttributes,
            'id' => $this->getId(),
            'content' => Components::make(
                $this->getComponents()
            ),
        ];
    }
}
