<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\Handler;
use MoonShine\Handlers\ImportHandler;

trait ResourceModelActions
{
    /**
     * @return string[]
     */
    public function getActiveActions(): array
    {
        return ['create', 'view', 'update', 'delete', 'massDelete'];
    }

    /**
     * @return list<ActionButton>
     */
    public function actions(): array
    {
        return [];
    }

    public function export(): ?ExportHandler
    {
        if (! config('moonshine.model_resources.default_with_export', true)) {
            return null;
        }

        $handler =  ExportHandler::make(__('moonshine::ui.export'));

        if (config('moonshine.export.format') !== 'xlsx') {
            $handler->csv();
        }

        return $handler;
    }

    public function import(): ?ImportHandler
    {
        if (! config('moonshine.model_resources.default_with_import', true)) {
            return null;
        }

        return ImportHandler::make(__('moonshine::ui.import'));
    }

    /**
     * @return list<Handler>
     */
    protected function handlers(): array
    {
        return array_filter([
            $this->export(),
            $this->import(),
        ]);
    }
}
