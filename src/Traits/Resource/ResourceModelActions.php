<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;

trait ResourceModelActions
{
    public function getActiveActions(): array
    {
        return ['create', 'show', 'edit', 'delete'];
    }

    public function actions(): array
    {
        return [];
    }

    public function getActions(): ActionButtons
    {
        return ActionButtons::make($this->actions());
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
