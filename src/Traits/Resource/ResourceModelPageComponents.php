<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

trait ResourceModelPageComponents
{
    public function getIndexPageComponents(): array
    {
        return empty($this->indexPageComponents()) ? $this->pageComponents() : $this->indexPageComponents();
    }

    public function getFormPageComponents(): array
    {
        return empty($this->formPageComponents()) ? $this->pageComponents() : $this->formPageComponents();
    }

    public function getDetailPageComponents(): array
    {
        return empty($this->detailPageComponents()) ? $this->pageComponents() : $this->detailPageComponents();
    }

    public function indexPageComponents(): array
    {
        return [];
    }

    public function formPageComponents(): array
    {
        return [];
    }

    public function detailPageComponents(): array
    {
        return [];
    }

    public function pageComponents(): array
    {
        return [];
    }
}
