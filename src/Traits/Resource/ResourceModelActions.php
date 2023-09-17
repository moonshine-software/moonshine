<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;

trait ResourceModelActions
{
    public function getActiveActions(): array
    {
        return ['create', 'show', 'edit', 'delete'];
    }

    /**
     * @return array<ActionButton>
     */
    public function actions(): array
    {
        return [];
    }

    public function export(): ?ExportHandler
    {
        return ExportHandler::make(__('moonshine::ui.export'))
            ->csv();
    }

    public function import(): ?ImportHandler
    {
        return ImportHandler::make(__('moonshine::ui.import'));
    }

    protected function handlers(): array
    {
        return array_filter([
            $this->export(),
            $this->import(),
        ]);
    }
}
