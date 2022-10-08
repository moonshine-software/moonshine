<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Resources;

use JsonSerializable;
use Leeto\MoonShine\Views\Views;

interface ResourceContract extends JsonSerializable
{
    public function title(): string;

    public function uriKey(): string;

    public function views(): Views;

    public function resolveRoutes(): void;

    public function getDataInstance();

    public function getData($id);
}
