<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Handlers\ExportHandler;
use MoonShine\Laravel\Handlers\Handler;
use MoonShine\Laravel\Handlers\Handlers;
use MoonShine\Laravel\Handlers\ImportHandler;
use MoonShine\Support\ListOf;

trait ResourceModelActions
{
    protected static bool $defaultExportToCsv = false;

    public static function defaultExportToCsv(): void
    {
        self::$defaultExportToCsv = true;
    }

    /**
     * @return ListOf<Action>
     */
    public function activeActions(): ListOf
    {
        return new ListOf(Action::class, [
            Action::CREATE,
            Action::VIEW,
            Action::UPDATE,
            Action::DELETE,
            Action::MASS_DELETE,
        ]);
    }

    /**
     * @return list<Action>
     */
    protected function getActiveActions(): array
    {
        return $this->activeActions()->toArray();
    }

    public function hasAction(Action ...$actions): bool
    {
        return collect($actions)->every(fn (Action $action): bool => in_array($action, $this->getActiveActions()));
    }

    public function export(): ?Handler
    {
        if (! moonshineConfig()->isDefaultWithExport()) {
            return null;
        }

        return ExportHandler::make(__('moonshine::ui.export'))->when(
            self::$defaultExportToCsv,
            static fn (ExportHandler $handler): ExportHandler => $handler->csv()
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
     * @return ListOf<Handler>
     */
    protected function handlers(): ListOf
    {
        return new ListOf(Handler::class, array_filter([
            $this->export(),
            $this->import(),
        ]));
    }

    public function getHandlers(): Handlers
    {
        return Handlers::make($this->handlers()->toArray())
            ->each(fn (Handler $handler): Handler => $handler->setResource($this));
    }
}
