<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages;

use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\Traits\ImportExportHandlersConcern;
use MoonShine\Laravel\Pages\Crud\IndexPage;

class TestIndexPage extends IndexPage
{
    use ImportExportHandlersConcern;

    protected ?string $alias = 'index-page';

    public array $testFilters = [];

    protected function filters(): iterable
    {
        return $this->testFilters;
    }

    protected function export(): ?Handler
    {
        return ExportHandler::make(__('moonshine::ui.export'))
            ->csv()
            ->filename($this->getResource()->getUriKey());
    }
}
