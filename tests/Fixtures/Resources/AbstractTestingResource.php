<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Resources\ModelResource;

abstract class AbstractTestingResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    public function isExportToCsv(): bool
    {
        return true;
    }

    public function getUriKey(): string
    {
        return parent::getUriKey();
    }
}
