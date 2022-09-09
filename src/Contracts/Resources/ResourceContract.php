<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Resources;

use Leeto\MoonShine\Views\Views;

interface ResourceContract
{
    public function title(): string;

    public function uriKey(): string;

    public function views(): Views;

    public function getDataInstance();

    public function getData($id);

    public function resolveRoutes(): void;
}
