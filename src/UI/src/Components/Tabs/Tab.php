<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Tabs;

use Closure;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Exceptions\ComponentException;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Throwable;

/**
 * @method static static make(Closure|string|iterable $labelOrComponents = [], iterable $components = [])
 */
class Tab extends AbstractWithComponents implements HasLabelContract, HasIconContract
{
    use WithLabel;
    use WithIcon;

    public bool $active = false;

    public ?string $id = null;

    private ComponentAttributesBagContract $labelAttributes;

    /**
     * @param  (Closure(static): string)|string|iterable<array-key, ComponentContract>  $labelOrComponents
     * @param  iterable<array-key, ComponentContract>  $components
     *
     * @throws Throwable
     */
    public function __construct(
        Closure|string|iterable $labelOrComponents = [],
        iterable $components = [],
    ) {
        if (is_iterable($labelOrComponents)) {
            /** @var iterable<array-key, ComponentContract> $labelOrComponents */
            $components = $labelOrComponents;
        } else {
            $this->setLabel($labelOrComponents);
        }

        $this->labelAttributes = new MoonShineComponentAttributeBag();

        parent::__construct($components);
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     */
    public function labelAttributes(array $attributes): static
    {
        $this->labelAttributes = $this->labelAttributes->merge($attributes);

        return $this;
    }

    /**
     * @throws ComponentException
     */
    public function getView(): string
    {
        throw ComponentException::tabsAreNotRendering();
    }

    public function active(Closure|bool|null $condition = null): static
    {
        $this->active = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id ?? (string) spl_object_id($this);
    }

    protected function prepareBeforeRender(): void
    {
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

    protected function prepareBeforeSerialize(): void
    {
        $this->prepareBeforeRender();
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'icon' => $this->getIcon(6),
            'label' => $this->getLabel(),
            'labelAttributes' => $this->labelAttributes,
            'id' => $this->getId(),
            'content' => Components::make(
                $this->getComponents()
            ),
        ];
    }
}
