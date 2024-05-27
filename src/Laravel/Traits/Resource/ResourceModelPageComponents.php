<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\UI\Fields\Field;

trait ResourceModelPageComponents
{
    /**
     * @return Field
     */
    public function getIndexPageComponents(): array
    {
        return empty($this->indexPageComponents()) ? $this->pageComponents() : $this->indexPageComponents();
    }

    /**
     * @return Field
     */
    public function getFormPageComponents(): array
    {
        return empty($this->formPageComponents()) ? $this->pageComponents() : $this->formPageComponents();
    }

    /**
     * @return Field
     */
    public function getDetailPageComponents(): array
    {
        return empty($this->detailPageComponents()) ? $this->pageComponents() : $this->detailPageComponents();
    }

    /**
     * @return Field
     */
    public function indexPageComponents(): array
    {
        return [];
    }

    /**
     * @return Field
     */
    public function formPageComponents(): array
    {
        return [];
    }

    /**
     * @return Field
     */
    public function detailPageComponents(): array
    {
        return [];
    }

    /**
     * @return Field
     */
    public function pageComponents(): array
    {
        return [];
    }
}
