<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Resources\ModelResource;

abstract class AbstractTestingResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected function export(): ?Handler
    {
        return ExportHandler::make(__('moonshine::ui.export'))
            ->csv()
            ->filename($this->getUriKey());
    }

    public function setTestPolicy(bool $value): static
    {
        $this->withPolicy = $value;

        return $this;
    }
}
