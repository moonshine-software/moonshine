<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\UI\Fields\Field;

trait ResourceModelPageComponents
{
    /**
     * @return list<Field>
     */
    public function getIndexPageComponents(): array
    {
        return empty($this->indexPageComponents()) ? $this->pageComponents() : $this->indexPageComponents();
    }

    /**
     * @return list<Field>
     */
    public function getFormPageComponents(): array
    {
        return empty($this->formPageComponents()) ? $this->pageComponents() : $this->formPageComponents();
    }

    /**
     * @return list<Field>
     */
    public function getDetailPageComponents(): array
    {
        return empty($this->detailPageComponents()) ? $this->pageComponents() : $this->detailPageComponents();
    }

    /**
     * @return list<Field>
     */
    public function indexPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<Field>
     */
    public function formPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<Field>
     */
    public function detailPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<Field>
     */
    public function pageComponents(): array
    {
        return [];
    }
}
