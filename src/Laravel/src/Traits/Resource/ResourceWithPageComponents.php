<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\UI\FieldContract;

trait ResourceWithPageComponents
{
    /**
     * @return list<FieldContract>
     */
    public function getIndexPageComponents(): array
    {
        return empty($this->indexPageComponents()) ? $this->pageComponents() : $this->indexPageComponents();
    }

    /**
     * @return list<FieldContract>
     */
    public function getFormPageComponents(): array
    {
        return empty($this->formPageComponents()) ? $this->pageComponents() : $this->formPageComponents();
    }

    /**
     * @return list<FieldContract>
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
     * @return list<FieldContract>
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
     * @return list<FieldContract>
     */
    protected function pageComponents(): array
    {
        return [];
    }

    public function modifyFormComponent(RenderableContract $component): RenderableContract
    {
        return $component;
    }

    public function modifyListComponent(RenderableContract $component): RenderableContract
    {
        return $component;
    }

    public function modifyDetailComponent(RenderableContract $component): RenderableContract
    {
        return $component;
    }
}
