<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

interface CrudResourceContract
{
    public function getIndexPage(): ?PageContract;

    public function getFormPage(): ?PageContract;

    public function getDetailPage(): ?PageContract;
}
