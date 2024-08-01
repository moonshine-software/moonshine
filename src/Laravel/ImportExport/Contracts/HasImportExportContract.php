<?php

declare(strict_types=1);

namespace MoonShine\Laravel\ImportExport\Contracts;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Handlers\Handler;
use Throwable;

interface HasImportExportContract
{
    public function export(): ?Handler;

    public function import(): ?Handler;

    /**
     * @return list<FieldContract>
     */
    public function exportFields(): array;

    /**
     * @throws Throwable
     */
    public function getExportFields(): Fields;

    /**
     * @return list<FieldContract>
     */
    public function importFields(): array;

    /**
     * @throws Throwable
     */
    public function getImportFields(): Fields;
}
