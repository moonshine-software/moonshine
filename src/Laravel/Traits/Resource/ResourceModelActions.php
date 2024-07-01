<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Core\Handlers\Handler;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Handlers\ExportHandler;
use MoonShine\Laravel\Handlers\ImportHandler;
use MoonShine\UI\Components\ActionButton;

trait ResourceModelActions
{
    protected static bool $defaultExportToCsv = false;

    /**
     * @return list<Action>
     */
    public function getActiveActions(): array
    {
        return [
            Action::CREATE,
            Action::VIEW,
            Action::UPDATE,
            Action::DELETE,
            Action::MASS_DELETE,
        ];
    }

    public function hasAction(Action ...$actions): bool
    {
        return collect($actions)->every(fn (Action $action): bool => in_array($action, $this->getActiveActions()));
    }

    /**
     * @return list<ActionButton>
     */
    public function topButtons(): array
    {
        return [];
    }

    public static function defaultExportToCsv(): void
    {
        self::$defaultExportToCsv = true;
    }

    public function export(): ?Handler
    {
        if (! moonshineConfig()->isDefaultWithExport()) {
            return null;
        }

        return ExportHandler::make(__('moonshine::ui.export'))->when(
            self::$defaultExportToCsv,
            static fn(ExportHandler $handler) => $handler->csv()
        );
    }

    public function import(): ?Handler
    {
        if (! moonshineConfig()->isDefaultWithImport()) {
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
