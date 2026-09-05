<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Pages\TestIndexPage;

abstract class AbstractTestingResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected function pages(): array
    {
        return [TestIndexPage::class, FormPage::class, DetailPage::class];
    }

    public function setTestPolicy(bool $value): static
    {
        $this->withPolicy = $value;

        return $this;
    }
}
