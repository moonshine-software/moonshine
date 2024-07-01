<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\Handler;
use MoonShine\Handlers\ImportHandler;

trait ResourceModelActions
{
    protected static bool $defaultExportToCsv = false;

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

    public static function defaultExportToCsv(): void
    {
        self::$defaultExportToCsv = true;
    }

    public function export(): ?ExportHandler
    {
        if (! config('moonshine.model_resources.default_with_export', true)) {
            return null;
        }

        return ExportHandler::make(__('moonshine::ui.export'))->when(
            self::$defaultExportToCsv,
            static fn (ExportHandler $handler): ExportHandler => $handler->csv()
        );
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
