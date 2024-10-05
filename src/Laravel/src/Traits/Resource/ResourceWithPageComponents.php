<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;

trait ResourceWithPageComponents
{
    /**
     * @return list<ComponentContract>
     */
    public function getIndexPageComponents(): array
    {
        return empty($this->indexPageComponents()) ? $this->pageComponents() : $this->indexPageComponents();
    }

    /**
     * @return list<ComponentContract>
     */
    public function getFormPageComponents(): array
    {
        return empty($this->formPageComponents()) ? $this->pageComponents() : $this->formPageComponents();
    }

    /**
     * @return list<ComponentContract>
     */
    public function getDetailPageComponents(): array
    {
        return empty($this->detailPageComponents()) ? $this->pageComponents() : $this->detailPageComponents();
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function formPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function pageComponents(): array
    {
        return [];
    }

    public function modifyFormComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    public function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    public function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }
}
