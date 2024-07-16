<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\Field;

trait ResourceModelPageComponents
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
    public function indexPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<FieldContract>
     */
    public function formPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<FieldContract>
     */
    public function detailPageComponents(): array
    {
        return [];
    }

    /**
     * @return list<FieldContract>
     */
    public function pageComponents(): array
    {
        return [];
    }
}
