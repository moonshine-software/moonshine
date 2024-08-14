<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\Components\FormBuilder;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Field;

trait ResourceModelPageComponents
{
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function getIndexPageComponents(): array
    {
        return empty($this->indexPageComponents()) ? $this->pageComponents() : $this->indexPageComponents();
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function getFormPageComponents(): array
    {
        return empty($this->formPageComponents()) ? $this->pageComponents() : $this->formPageComponents();
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function getDetailPageComponents(): array
    {
        return empty($this->detailPageComponents()) ? $this->pageComponents() : $this->detailPageComponents();
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function formPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function detailPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function pageComponents(): array
    {
        return [];
    }

    /**
     * @param FormBuilder $component
     *
     * @return FormBuilder
     */
    public function modifyFormComponent(MoonShineRenderable $component): MoonShineRenderable
    {
        return $component;
    }

    /**
     * @param TableBuilder $component
     *
     * @return TableBuilder
     */
    public function modifyListComponent(MoonShineRenderable $component): MoonShineRenderable
    {
        return $component;
    }

    /**
     * @param TableBuilder $component
     *
     * @return TableBuilder
     */
    public function modifyDetailComponent(MoonShineRenderable $component): MoonShineRenderable
    {
        return $component;
    }
}
