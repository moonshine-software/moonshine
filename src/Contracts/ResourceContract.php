<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

use Leeto\MoonShine\Fields\Fields;

interface ResourceContract
{
    public function title(): string;

    public function fields(): array;

    public function fieldsCollection(): Fields;

    public function filters(): array;

    public function actions(): array;

    public function search(): array;

    public function resolveRoutes(): void;

    public function uriKey(): string;
}
